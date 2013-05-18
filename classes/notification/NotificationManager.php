<?php
/*
 * Created on May 9, 2009
 * Author: Yoni Rosenbaum
 *
 * Responsible for sending email notifications.
 */

class NotificationManager {
    private static $theInstance;

    /**
     * Send the given notification via email.
     *
     * @param Notification $notification the notificiation to send
     * @return boolean true on success, false if there was an error.
     */
    public static function send($notification) {
        return self::getInstance()->send($notification);
    }

    private static function getInstance() {
        if (!self::$theInstance) {
            self::$theInstance = new EmailNotificationManager();
        }
        return self::$theInstance;
    }
}