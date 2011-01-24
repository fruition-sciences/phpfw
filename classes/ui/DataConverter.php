<?php
/*
 * Created on Jul 4, 2008
 * Author: Yoni Rosenbaum
 * 
 * Responsible for converting fomatted data (such as dates and numbers) using
 * locale & timezone.
 * 
 * Currently, uses only timezone.
 */

class DataConverter {
    private static $theInstance;
    private $timezoneName; // String

    public function DataConverter($timezoneName) {
        $this->timezoneName = $timezoneName;
    }

    /**
     * Returns the DataConverter instance. Creates a new one if doesn't exist yet.
     * Uses the timezone of the user of the current transaction.
     * @return DataConverter
     */
    public static function getInstance() {
        if (!self::$theInstance) {
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
            self::$theInstance = new DataConverter($timezone);
        }
        return self::$theInstance;
    }

    /**
     * Parse the given formatted date according to the timezone set in this
     * DataConverter object.
     * 
     * @param String $formattedDate formatted date (or date & time)
     * @return long unix timestamp
     */
    public function parseDate($formattedDate) {
        if (!$formattedDate) {
            return null;
        }
        $originalDefaulyTimezone = date_default_timezone_get();
        // Temporarily change time zone.
        date_default_timezone_set($this->timezoneName);

        $date = new DateTime($formattedDate);
        $tz = new DateTimeZone($this->timezoneName);
        $date->setTimezone($tz);

        // Set default time zone back
        date_default_timezone_set($originalDefaulyTimezone);
        return $date->format('U');
    }
    
    /**
     * Parse the given formatted time.
     * 
     * @param String $formattedDate formatted time "HH:MM:SS"
     * @return long number of seconds since midnight.
     */
    public static function parseTime($formattedTime){
        list($hours,$mins,$secs) = explode(':',$formattedTime);
        $seconds = $hours * 3600 + $mins * 60 + $secs;
        return $seconds;
    }

    public function getTimeZoneName() {
        return $this->timezoneName;
    }
}