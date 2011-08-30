<?php
/*
 * Created on May 9, 2009
 * Author: Yoni Rosenbaum
 *
 * Responsible for sending email (or fax) notifications.
 */

class NotificationManager {
    /**
     * Send the given notification.
     *
     * @param Notification $notification the notificiation to send
     * @return boolean true on success, false if there was an error.
     */
    public static function send($notification) {
        self::validateNotification($notification);
        $recipientList = self::getAllRecipients($notification);
        $recipientListStr = implode(',', $recipientList);
        // Defines who really gets this email
        $recipients = array('To' => $recipientListStr);

        // Call getContent before getting the Subject. (It may get set by getContent).
        $content = $notification->getContent();
        $headers = array (
             'From'    => $notification->getFrom(),
             'Subject' => $notification->getSubject(),
             'To'      => $notification->getRecipient()
        );
        // Adding 'Cc' to the header for display purpose
        if ($notification->getCCList()) {
            $ccListStr = implode(',', $notification->getCCList());
            $headers['Cc'] = $ccListStr;
        }
        $mime = new Mail_mime("\n");

        if ($content) {
            $mime->setTXTBody($content);
        }
        foreach ($notification->getAttachments() as $attachment) {
            $result = self::addAttachment($mime, $attachment);
            if (PEAR::isError($result)) {
                Logger::error("Send email failed. " . $result->getMessage());
                return false;
            }
        }
        $body = $mime->get();
        $headers = $mime->headers($headers);
        $emailEnabled = Config::getInstance()->getBoolean("email/enabled", true);
        if (!$emailEnabled) {
            return true;
        }
        $params = self::getMailConfigParams();
        $mailer =& Mail::factory('smtp', $params);
        $sendResult = $mailer->send($recipients, $headers, $body);
        if (PEAR::isError($sendResult)) {
            Logger::error("Send email failed. " . $sendResult->getMessage());
            return false;
        }
        Logger::info("Email sent to " . $notification->getRecipient() . ". Subject: " . $notification->getSubject());
        return true;
    }

    private static function getMailConfigParams() {
        $params = array();
        $config = Config::getInstance();
        $params['host'] = $config->getString('email/smtp_host', 'localhost');
        $port = $config->getString('email/smtp_port', null);
        if ($port) {
            $params['port'] = $port;
        }
        $authRequired = $config->getBoolean('email/smtp_auth', false);
        if ($authRequired) {
            $params['auth'] = $authRequired;
            $params['username'] = $config->getString('email/smtp_username');
            $params['password'] = $config->getString('email/smtp_password');
        }
        $params['timeout'] = "30";
        return $params;
    }
    

    /**
     * Generate a list of all recipients for this email.
     * This includes:
     * 1. The recipient field (which may be comma separated)
     * 2. The ccList
     * 3. The bccList
     *
     * @param $notification
     * @return array of strings.
     */
    private static function getAllRecipients($notification) {
    	$toList = preg_split('/\s+,\s+/', $notification->getRecipient());
    	$recipientList = array_merge($toList, $notification->getCCList(), $notification->getBCCList());
    	return $recipientList;
    }

    /**
     * Add the given attachment to the given Mail_mime object.
     *
     * @param Mail_mime $mime
     * @param IAttachment $attachment
     * @return Boolean|PEAR_Error True on success 
     */
    private static function addAttachment($mime, $attachment) {
        if ($attachment instanceof ContentAttachment) {
            $result = $mime->addAttachment($attachment->getContent(), $attachment->getContentType(), $attachment->getFileName(), false);
        }
        else if ($attachment instanceof FileAttachment) {
            $result = $mime->addAttachment($attachment->getFilePath());
        }
        return $result;
    }

    private static function validateNotification($notification) {
        if (!$notification->getFrom()) {
            throw new IllegalStateException("Missing 'from' field in notification");
        }
        if (!$notification->getRecipient()) {
            throw new IllegalStateException("Missing recipient in notification");
        }
    }
}