<?php


class PushNotificationiOSManager implements INotificationManager{
    
    const INVALID_TOKEN = 8;

    /**
     * Contain an array of two values: the bean name and method name.
     * This array is use to make a callback if an error is catch during
     * sending notification process.
     * @var Array $handlerErrorCallBack
     */
    private $handlerErrorCallBack;

    /**
     * Send the given notification with Apple Push Notification Service Server.
     *
     * @param Notification $notification the notification to send
     * @return boolean true on success, false if there was an error.
     */
    public function send($notification) {
        $config = Config::getInstance();
        $appRoot = $config->getString('appRootDir');
        

        // Instanciate a new ApnsPHP_Push object, with the provider certificate
        $push = new ApnsPHP_Push(
                ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
                $appRoot .$config->getString('monitoring/notification/push/providerCertificateDir')
        );

        // Set the Provider Certificate passphrase
        $push->setProviderCertificatePassphrase($config->getString('monitoring/notification/push/passphrase'));
        // Set the Root Certificate Autority to verify the Apple remote peer
        $push->setRootCertificationAuthority($appRoot .$config->getString('monitoring/notification/push/rootCertificateAuthorityDir'));

        // Connect to the Apple Push Notification Service
        $push->connect();

        // Create a message for each device
        $message = new ApnsPHP_Message();
        $message->setText($notification->getContent());
        $message->setSound();

        $recipientList = explode(",", $notification->getRecipient());
        foreach ($recipientList as $registrationId) {
            echo $registrationId;
            $message->addRecipient($registrationId);
        }
        // Add the message to the message queue
        $push->add($message);
         

        // Send all messages in the message queue
        $push->send();

        // Disconnect from the Apple Push Notification Service
        $push->disconnect();
         
        // Examine the error message container. 
        $aErrorQueue = $push->getErrors();
        if (!empty($aErrorQueue)) {
            foreach ($aErrorQueue as $error) {
                foreach ($error['ERRORS'] as $err) {
                    //For statusCode = 8, which is Invalid Token, we delete the token.
                    if ($err['statusCode'] == self::INVALID_TOKEN) {
                        if ($this->handlerErrorCallBack) {
                            $token = $error['MESSAGE']->getRecipient();
                            call_user_func($this->handlerErrorCallBack, $token);
                        }
                    }
                    Logger::error("Sending push notification failed. Error code: " . $err['statusCode'] . ". Message: " . $err['statusMessage']);
                }
            }
            return false;
        }

        return true;
    }

    public function setHandlerErrorCallBack($className, $methodName) {
        $this->handlerErrorCallBack = array($className, $methodName);
    }

}

