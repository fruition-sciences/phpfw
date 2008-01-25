<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Formatter {
    private static $theInstance;
    
    public static function getInstance() {
        if (!self::$theInstance) {
            self::$theInstance = new Formatter();
        }
        return self::$theInstance;
    }
    
    public function date($date) {
        if (!isset($date) || $date == "") {
            return "";
        }
        return date("m/d/Y", $date);
    }

    public function dateTime($date) {
        if (!isset($date) || $date == "") {
            return "";
        }
        return date("m/d/Y g:i:s A", $date);
    }
}
