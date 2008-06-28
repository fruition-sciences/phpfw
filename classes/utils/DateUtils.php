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
}