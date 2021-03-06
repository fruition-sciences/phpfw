<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 *
 */

class SQLUtils {
    public static function escapeString($str, $withQuotes=true) {
        if ($str === null) {
            return "null";
        }
        $db = Transaction::getInstance()->getDB();
        $escaped = '';
        if (method_exists($db, 'getDB')) {
            $caller = self::getCallerFunction();
            Logger::warning("Depracated: Avoid SQLUtils::escapeString. Use prepared statement. Called by: ${caller}");
            $escaped = $db->getDB()->real_escape_string($str);
        }
        else {
            $escaped = mysql_real_escape_string($str);
        }
        if ($withQuotes) {
            $escaped = "'" . $escaped . "'";
        } 
        return $escaped;
    }

    /**
     * Convert a date (number) to a database date representation (string).
     *
     * @param long dateTime The date (number)
     * @param boolean whether the returned string should be surrounted by quotes.
     * @return string
     */
    public static function convertDate($dateTime, $timeZone='GMT', $withQuotes=true) {
        if ($dateTime == null) {
            return $withQuotes ? "null" : null;
        }
        $format = new Formatter($timeZone);
        $sDate = $format->dateFormat($dateTime, "Y-m-d H:i:s");
        return $withQuotes ? "'$sDate'" : $sDate;
    }
    
    /**
     * Convert a time in seconds (number) to a database time 
     * representation (string HH:MM:SS).
     *
     * @param long time The time (seconds number)
     * @param boolean whether the returned string should be surrounted by quotes.
     */
    public static function convertTime($time, $withQuotes=true) {
        if ($time == null || $time === "") {
            return "null";
        }
        $sTime = sprintf("%02d%s%02d%s%02d", floor($time/3600), ":", ($time/60)%60, ":", $time%60);
        return $withQuotes ? "'$sTime'" : $sTime;
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

    /**
     * Convert the given number to an id, which basically means converting -1
     * and empty string to null.
     * 
     * @param int $val
     * @param string $quoteNull if true, the 'null' value will be in quotes (useful
     *        when NOT using prepared statements). Pass false when you use a
     *        prepared statement. 
     * @return Ambigous <string, NULL>|unknown
     */
    public static function convertId($val, $quoteNull=true) {
        if ($val < 0 || $val === null || $val === "") {
            return $quoteNull ? "null" : null;
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
        // Surround content with quotes, unless it's '?', meant for prepared statement.
        if ($geom != '?') {
            $geom = "'${geom}'";
        }
        return "GeomFromText(${geom} ,4326)";
    }

    /**
     * Get the caller method.
     * This should eventually be moved to a more general utils class.
     */
    private static function getCallerFunction() {
        $callers = debug_backtrace();
        if ($callers && count($callers > 1)) {
            $caller = $callers[1];
            $file = $caller['file'];
            $line = $caller['line'];
            return "${file} line ${line}";
        }
        return null;
    }
    
    /**
     * If you want to use call_user_func_array and bind_param then you need
     * to pass array values as reference.
     * This function create a new array using the given array where all the
     * values are references.
     * 
     * @see http://stackoverflow.com/questions/3681262/php5-3-mysqli-stmtbind-params-with-call-user-func-array-warnings
     * @param array $arr
     * @return array Array with value as reference.
     */
    public static function referenceValues($arr) {
        $refs = array();
        foreach ($arr as $k => $v) {
            $refs[$k] = &$arr[$k];
        }
        return $refs;
    }
}