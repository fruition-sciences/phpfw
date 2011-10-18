<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 *
 */

class SQLUtils {
    public static function escapeString($str) {
        if ($str === null) {
            return "null";
        }
        return "'" . mysql_escape_string($str) . "'";
    }
      
    /**
     * Return string without adding simple quotes
     * Needed to use the GeomFromText function in MySql
     * 
     * @param string $str
     */
    public static function escapeStringPolygon($str) {
        return $str;
    }
    
    public static function escapeStringPoint($str) {
        return $str;
    }

    /**
     * Convert a date (number) to a database date representation (string).
     *
     * @param long dateTime The date (number)
     */
    public static function convertDate($dateTime, $timeZone='GMT') {
        if ($dateTime == null) {
            return "null";
        }
        $format = new Formatter($timeZone);
        return "'" . $format->dateFormat($dateTime, "Y-m-d H:i:s") . "'";
    }
    
    /**
     * Convert a time in seconds (number) to a database time 
     * representation (string HH:MM:SS).
     *
     * @param long time The time (seconds number)
     */
    public static function convertTime($time) {
        if ($time == null || $time === "") {
            return "null";
        }
        return "'" . sprintf("%02d%s%02d%s%02d", floor($time/3600), ":", ($time/60)%60, ":", $time%60) . "'";
    }

    public static function convertLong($val) {
        if ($val === null || $val === "") {
            return "null";
        }
        return $val;
    }

    public static function convertDouble($val) {
        if ($val === null || $val === "") {
            return "null";
        }
        return $val;
    }

    public static function convertId($val) {
        if ($val < 0 || $val === null || $val === "") {
            return "null";
        }
        return $val;
    }

    public static function convertBoolean($val) {
        return $val ? 1 : 0;
    }
}