<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Formatter {
    /**
     * @var Formatter
     */
    private static $theInstance;

    /**
     * @var DateTimeZone
     */
    private $timezone;

    /**
     * @var Zend_Locale
     */
    private $zendLocale;

    /**
     * @var DateTimeZone
     */
    private static $utcTimeZone;

    /**
     * Create a new Formatter, for the given time zone.
     *
     * @param String $timezoneName For example: 'America/Los_Angeles'
     */
    public function Formatter($timezoneName, $localeName) {
        $this->timezone = new DateTimeZone($timezoneName);
        $this->zendLocale = new Zend_Locale($localeName);
    }

    /**
     * Returns the formatter instance. Creates a new one if doesn't exist yet.
     * Uses the timezone of the user of the current transaction.
     * 
     * @return Formatter
     */
    public static function getInstance() {
        if (!self::$theInstance) {
            $user = Transaction::getInstance()->getUser();
            self::$theInstance = new Formatter($user->getTimezone(), $user->getLocale());
        }
        return self::$theInstance;
    }

    /**
     * Format the given timestamp as date.
     *
     * @param long $timestamp unix timestamp
     * @return String formatted date, of the given timestamp in the timezone set
     *         for this Formatter object.
     */
    public function date($timestamp) {
        return $this->dateFormat($timestamp, "m/d/y");
    }

    /**
     * Format the given timestamp as date & time.
     *
     * @return String formatted date and time, of the given timestamp in the
     *         timezone set for this Formatter object.
     */
    public function dateTime($timestamp, $newLine=false, $showSeconds=false) {
        $time = $showSeconds ? "g:i:s" : "g:i";
        return $this->dateFormat($timestamp, $newLine ? "m/d/y<b\\r/>$time a" : "m/d/y $time a");
    }
    
    /**
     * Format the given timestamp as time.
     *
     * @return String formatted time, of the given timestamp in the
     *         timezone set for this Formatter object.
     */
    public function time($timestamp) {
        return $this->dateFormat($timestamp, "g:i a");
    }
    
    public function secondsToTime($seconds) {
        $timeStr = substr(SQLUtils::convertTime($seconds), 1, -1);
        return $timeStr == "null" ? "" : $timeStr;
    }

    /**
     * Format the given timestamp using the given format string.
     *
     * @return String formatted date of the given timestamp in the timezone set
     *         for this Formatter object.
     */
    public function dateFormat($timestamp, $formatString) {
        if (!isset($timestamp) || $timestamp == "") {
            return "";
        }
        try {
            $date = new DateTime(date('c', $timestamp));
            $date->setTimezone($this->timezone);
            return $date->format($formatString);
        }
        catch (Exception $e) {
            return "";
        }
    }

    /**
     * Format the given timestamp in UTC (GMT).
     *
     * @return String the formatted date/time.
     */
    public static function dateTimeUTC($timestamp) {
        $date = new DateTime(date('c', $timestamp));
        $date->setTimezone(self::getUTCTimeZone());
        return $date->format("Y-m-d H:i:s T");
    }

    public function number($val, $digits=null) {
        return Zend_Locale_Format::toNumber(
            $val,
            array('locale' => $this->zendLocale,
                  'precision' => $digits));
    }

    /**
     * Get the name of this formatter's timezone.
     * 
     * @return String
     */
    public function getTimeZoneName() {
        return $this->timezone->getName();
    }

    /**
     * Get this formatter's timezone.
     * 
     * @return DateTimeZone
     */
    public function getTimeZone() {
        return $this->timezone;
    }

    private static function getUTCTimeZone() {
        if (!self::$utcTimeZone) {
            self::$utcTimeZone = new DateTimeZone('UTC');
        }
        return self::$utcTimeZone;
    }
}
