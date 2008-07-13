<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class SQLUtils {
    public static function escapeString($str) {
        if ($str == null) {
            return "null";
        }
        return "'" . mysql_escape_string($str) . "'";
    }

    /**
     * Convert a date (number) to a database date representation (string). 
     * 
     * @param long dateTime The date (number)
     */
    public static function convertDate($dateTime) {
        if ($dateTime == null) {
            return "null";
        }
        // Note: Assumes that the timezone of PHP is UTC and that the database is in UTC.
        return "'" . date("Y-m-d H:i:s", $dateTime) . "'";
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