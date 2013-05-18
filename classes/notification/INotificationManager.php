<?php
/*
 * A notification manager interface.
 * 
 * A notification manager is responsible for delivering a notification via specific
 * method.
 * The notification object is assumed to have a recipient in a format which is
 * applicable for the delivery method.
 * 
 * Created on May 17, 2013
 * Author: Yoni Rosenbaum
 */
 
interface INotificationManager {
    /**
     * Send the given notification.
     *
     * @param Notification $notification the notificiation to send
     * @return boolean true on success, false if there was an error.
     */
    public function send($notification);
}