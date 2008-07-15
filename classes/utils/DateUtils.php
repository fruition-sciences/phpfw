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
        return $date->format("g");
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
        return $date->format('U');
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
     * Get a unix timestamp representing 12AM of the next date in the given
     * timezone.
     * 
     * @param long $date unix timestamp.
     * @param String $timezone time zone code.
     * @return long unix timestamp
     */
    public static function getBeginningOfYesterday($timestamp, $timezone) {
        return self::addDays($timestamp, -1, 0, 0, 0, $timezone);
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

	    if ($endTime > $startTime)
	    {
	        $diff = $endTime - $startTime;
	
	        $hours = floor($diff/3600);
	        $diff = $diff % 3600;
	
	        $minutes = floor($diff/60);
	        $diff = $diff % 60;
	
	        $seconds = $diff;
	
	        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
	    }
	    else
	    {
	        throw new IllegalArgumentException("Start time should be less than end time");
	    }
	}
}