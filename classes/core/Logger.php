<?php
/*
 * Created on May 23, 2008
 * Author: Yoni Rosenbaum
 * 
 */

class Logger {
    public static function info($msg) {
        self::writeMessage("INFO", $msg);
    }

    public static function error($msg, $exception=null) {
        if ($exception) {
            $msg .= "\n" . $exception->__toString();
        }
        self::writeMessage("ERROR", $msg);
    }

    public static function debug($msg) {
        self::writeMessage("DEBUG", $msg);
    }

    public static function warning($msg) {
        self::writeMessage("WARNING", $msg);
    }
    
    private static function writeMessage($type, $msg) {
        $pid = getmypid();
        error_log("$type: (pid=$pid) " . $msg);
    }
}