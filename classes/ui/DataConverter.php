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
    private $locale; // String

    public function DataConverter($timezoneName, $locale='en_US') {
        $this->timezoneName = $timezoneName;
        $this->locale = $locale;
    }

    /**
     * Returns the DataConverter instance. Creates a new one if doesn't exist yet.
     * Uses the timezone of the user of the current transaction.
     * @return DataConverter
     */
    public static function getInstance() {
        if (!self::$theInstance) { 
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
            $locale = Transaction::getInstance()->getUser()->getLocale();
            self::$theInstance = new DataConverter($timezone, $locale);
        }
        return self::$theInstance;
    }

    /**
     * Parse the given formatted date according to the timezone set in this
     * DataConverter object.
     * 
     * @param String $formattedDate formatted date (or date & time)
     * @param String $format the date format to use. See: http://framework.zend.com/manual/1.12/en/zend.date.constants.html#zend.date.constants.list
     * @return long unix timestamp
     */
    public function parseDate($formattedDate, $format=null) {
        if (!$formattedDate) {
            return null;
        }
        // This is quite bad. In order to parse date in a given timezone using
        // Zend_Date, looks like we must change the default time zone.
        // We're changing it back after this call.
        $defaultTimeZone = date_default_timezone_get();
        try {
            @date_default_timezone_set($this->timezoneName);
            $zendDate = new Zend_Date($formattedDate, $format, $this->locale);
            $timestamp = $zendDate->getTimestamp();
        }
        catch (Exception $e) {
            @date_default_timezone_set($defaultTimeZone);
            throw $e;
        }
        @date_default_timezone_set($defaultTimeZone);
        return $timestamp;
    }
    
    /**
     * Parse the given formatted time which is a duration (absolute amount of time).
     * 
     * @param String $formattedDate formatted time "HH:MM:SS"
     * @return long number of seconds.
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