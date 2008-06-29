<?php
/*
 * Created on Jun 27, 2008
 * Author: Yoni Rosenbaum
 * 
 */

class DateUtils {
    /**
     * Get a number representing the hour of the day [0-23].
     * 
     * @param long $date unix timestamp
     * @param String $timezone 
     */
    public static function getHourOfDay($date, $timezone) {
        // TODO: TIMEZONE
        $a = getdate($date);
        $hour = $a['hours'];
        return $hour;
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
        // TODO: TIMEZONE: Use timezone
        $date = new DateTime(date('c', $timestamp));
        $date->setTime(0, 0, 0);
        return $date->format('U');
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
        // TODO: TIMEZONE: Use timezone
        $date = new DateTime(date('c', $timestamp));
        $date->modify('-1 day');
        $date->setTime(0, 0, 0);
        return $date->format('U');
    }
}