<?php

/**
 * A custom Fruition Logger for APNS PHP.
 *
 * This logger outputs the messages to Fruition log files, prefixed with date,
 * service name (ApplePushNotificationService) and Process ID (PID).
 *
 * @ingroup ApnsPHP_Log
 */
class ApnsPHP_Log_Fruition implements ApnsPHP_Log_Interface
{
	/**
	 * Logs a message.
	 *
	 * @param  $sMessage @type string The message.
	 */
	public function log($sMessage) {
        $message = date('r') . 'ApnsPHP[' . getmypid() . ']' . trim($sMessage) . '\n';

        if (strpos(trim($sMessage),'ERROR') !== false) {
            Logger::error($message);
        } else if (strpos(trim($sMessage),'WARNING') !== false) {
            Logger::warning($message);
        } else {
            Logger::info($message);
        }
	    
	    
	}
}
