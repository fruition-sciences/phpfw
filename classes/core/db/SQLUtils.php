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
        $db = Transaction::getInstance()->getDB();
        $escaped = '';
        if (method_exists($db, 'getDB')) {
            Logger::warning("Depracated: Avoid SQLUtils::escapeString. Use prepared statement instead (suppoer for prepared statements coming soon).");
            $escaped = $db->getDB()->real_escape_string($str);
        }
        else {
            $escaped = mysql_real_escape_string($str);
        }
        return "'" . $escaped . "'";
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
    
    /**
     * 
     * Return the SQL function to insert a geometric feature from a WKT string
     * Format WKT : POINT(X Y) or POLYGON((X1 Y1, .... , Xn Yn , X1 Y1))
     * 4326 is the code for the "WGS84" projection, used for all the features
     * 
     * @param GeomPoint $geom | @param GeomPolygon $geom
     * @return string
     */
    public static function convertGeom($geom) {
        if ($geom === null) {
            return "null";
        }
        return "GeomFromText('" . $geom->toWKT() . "',4326)";
    }
}