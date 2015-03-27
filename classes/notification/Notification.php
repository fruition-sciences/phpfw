<?php
/*
 * Created on May 10, 2009
 * Author: Yoni Rosenbaum
 *
 * A simple notificiation. Can be used for email/fax/pager/mobile-push.
 * The recipient will be either email address, phone number or device id depending
 * on how you plan to send it.
 */

class Notification {
    const FORMAT_TEXT = 'txt';
    const FORMAT_HTML = 'html';

    private $recipient;
    private $ccList = array();
    private $bccList = array();
    private $from;
    private $subject;
    private $content;
    private $attachments = array(); // Array of IAttachment instances
    private $format;
    /**
     * @var INotificationManager
     */
    private $notificationManager;

    /**
     * Set the recepient for the message.
     * @param $recipient String email address. Can be comma separated list of addresses.
     */
    public function setRecipient($recipient) {
        $this->recipient = $recipient;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function addCC($cc) {
        $this->ccList[] = $cc;
    }

    public function getCCList() {
        return $this->ccList;
    }

    public function addBCC($bcc) {
        $this->bccList[] = $bcc;
    }

    public function getBCCList() {
        return $this->bccList;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function getFrom() {
        return $this->from;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function getSubject() {
        return $this->subject;
    }

    /**
     * Set the full content of this notification.
     *
     * @param String $content the content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Get the full content of this notification.
     *
     * @return String the content
     */
    public function getContent() {
        return $this->content;
    }

    public function setFormat($format) {
        $this->format = $format;
    }

    public function getFormat() {
        return $this->format;
    }

    /**
     * Add an attachment.
     *
     * @param Blob $content the attachment content
     * @param String $contentType the content type
     * @param String $fileName a file name for this attachment.
     */
    public function attachContent($content, $contentType, $fileName) {
        $attachment = new ContentAttachment();
        $attachment->setContent($content);
        $attachment->setContentType($contentType);
        $attachment->setFileName($fileName);
        $this->attachments[] = $attachment;
    }

    public function attachFile($filePath) {
        $attachment = new FileAttachment($filePath);
        $this->attachments[] = $attachment;
    }

    /**
     * Get all attachments.
     *
     * @return Array of IAttachment instances
     */
    public function getAttachments() {
        return $this->attachments;
    }

    /**
     * Set the notification manager to be used to send this notification.
     * If not set, the default notification manager will be used (email).
     * 
     * @param INotificationManager $notificationManager
     */
    public function setNotificationManager($notificationManager) {
        $this->notificationManager = $notificationManager;
    }

    /**
     * Get the notification manager that will be used to send this notification.
     * 
     * @return INotificationManager
     */
    public function getNotificationManager() {
        return $this->notificationManager;
    }

    /**
     * Send this notification. If set, uses the 'notificationManager'. Otherwise
     * uses the default notification manager (email).
     * 
     * @return boolean true on success, false if there was an error.
     */
    public function send() {
        if ($this->notificationManager) {
            return $this->notificationManager->send($this);
        }
        return NotificationManager::send($this);
    }
}