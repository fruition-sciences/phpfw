<?php
/*
 * Created on Jun 27, 2008
 * Author: Yoni Rosenbaum
 *
 */

class DateUtils {
    /**
     * Get a number representing the hour of the day [0-23] of the given unix
     * timestamp in the given timezone.
     *
     * @param long $date unix timestamp
     * @param String $timezone
     */
    public static function getHourOfDay($timestamp, $timezone) {
        $date = new DateTime(date('c', $timestamp));
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);
        return intval($date->format("G"));
    }

    /**
     * Add the given number of days to the given timestamp, and optionally, set
     * the time of the day to the given hours, minutes and seconds.
     *
     * @param long $timestamp unix timestamp
     * @param long $daysToAdd number of days to add (or substract, if negative)
     * @param long $hours (optional) the hour of the day to set.
     * @param long $minutes (optional) the minute of the day to set.
     * @param long $seconds (optional) the minute of the day to set.
     * @param String $timezone the timezone to evaluate the given time in. If null,
     *        the current user's account's timezone will be used.
     * @return long unix timestamp
     */
    public static function addDays($timestamp, $daysToAdd, $hours=null, $minutes=null, $seconds=null, $timezone=null) {
        if (!$timezone) {
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
        }
        $date = new DateTime(date('c', $timestamp));
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);
        if ($hours !== null || $minutes !== null || $seconds !== null) {
            if ($hours === null) {
                $hours = $date->format('g');
            }
            if ($minutes === null) {
                $minutes = $date->format('i');
            }
            if ($seconds === null) {
                $seconds = $date->format('s');
            }
            $date->setTime($hours, $minutes, $seconds);
        }
        $date->modify("$daysToAdd day");
        return (int)$date->format('U');
    }

    /**
     * Get a unix timestamp representing 12AM of the given date in the given
     * timezone.
     *
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfDay($timestamp, $timezone) {
        return self::addDays($timestamp, 0, 0, 0, 0, $timezone);
    }

    /**
     * Get a unix timestamp representing 12AM of the previous date in the given
     * timezone.
     *
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfPreviousDay($timestamp, $timezone) {
        return self::addDays($timestamp, -1, 0, 0, 0, $timezone);
    }
    
     /**
     * Get a unix timestamp representing 12AM of the first day of the date's week in the given
     * timezone.
     * Assume that the first day of the week is Monday.
     *
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfWeek($timestamp, $timezone){
        date_default_timezone_set($timezone);
        if(date("l",$timestamp) == "Monday"){
            return DateUtils::getBeginningOfDay($timestamp, $timezone);
        }else{
            return DateUtils::getBeginningOfDay(strtotime("last monday", $timestamp), $timezone);
        }
    }

    /**
     * Calculate time difference between 2 time stamps.
     *
     * @param long $startTime unix timestamp
     * @param long $endTime (optional) unix timestamp. Default is current time.
     * @return String string containing hours minutes and seconds.
     */
	public static function timeDiff($startTime, $endTime = null) {
	    $endTime = $endTime ? $endTime : time();

        $diff = $endTime - $startTime;
        $sign = "";
        if ($diff < 0) {
            $sign = "-";
            $diff = -$diff;
        }
        $hours = floor($diff/3600);
        $diff = $diff % 3600;

        $minutes = floor($diff/60);
        $diff = $diff % 60;

        $seconds = $diff;

        return $sign . str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
	}
	
   /**
     * Modify a timestamp using strtotime() textual datetime description.
     * @param $timestamp Timestamp to modify
     * @param $modifier String in a relative format accepted by strtotime(). ex : "+1 day", "+1 week 2 days 4 hours 2 seconds", "next Thursday"
     * @return Long modified Timestamp
     */
    public static function modifyTimestamp($timestamp, $modifier, $timezone=null){
        $dateTime = new DateTime(date('c', $timestamp));
        if($timezone){
            $tz = new DateTimeZone($timezone);
            $dateTime->setTimezone($tz);
        }
        $dateTime->modify($modifier);
        return $dateTime->format("U");
    }
    
    /**
     * Return an array containing all the months
     * The key is the month number and the value is the formatted month
     * @param string $format : F or m or M or n or t
     * @return array
     */
    public static function getMonthsArray($format){
        $months = array();
        for ($i = 1; $i <= 12; $i++){
            $months[$i] = date($format, mktime(0, 0, 0, $i+1, 0, 0, 0));
        }
        return $months;
    }

    /**
     * Create a new unix timestamp representing the given date/time in the given
     * timezone.
     *
     * @param $year int
     * @param $month int
     * @param $day int
     * @param $hour int
     * @param $minute int
     * @param $second int
     * @param $timezone (String) the timezone to evaluate the given time in. If
     *        null, the current user's account's timezone will be used.
     * @return DateTime
     */
    public static function makeDate($year, $month, $day, $hour=0, $minute=0, $second=0, $timezone=null) {
        if (!$timezone) {
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
        }
        $date = new DateTime();
        $tz = new DateTimeZone($timezone);
        $date->setTimezone($tz);
        $date->setDate($year, $month, $day);
        $date->setTime($hour, $minute, $second);
        return $date;
    }
}