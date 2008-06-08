<?php
/*
 * Created on May 23, 2008
 * Author: Yoni Rosenbaum
 * 
 */

class Logger {
    public static function info($msg) {
        error_log("INFO: " . $msg);
    }

    public static function error($msg, $exception=null) {
        if ($exception) {
            $msg .= "\n" . $exception->__toString();
        }
        error_log("ERROR: " . $msg);
    }

    public static function debug($msg) {
        error_log("DEBUG: " . $msg);
    }
}