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
     * @var DateTimeZone
     */
    private static $utcTimeZone;

    /**
     * Create a new Formatter, for the given time zone.
     *
     * @param String $timezoneName For example: 'America/Los_Angeles'
     */
    public function Formatter($timezoneName) {
        $this->timezone = new DateTimeZone($timezoneName);
    }

    /**
     * Returns the formatter instance. Creates a new one if doesn't exist yet.
     * Uses the timezone of the user of the current transaction.
     * 
     * @return Formatter
     */
    public static function getInstance() {
        if (!self::$theInstance) {
            $timezone = Transaction::getInstance()->getUser()->getTimezone();
            self::$theInstance = new Formatter($timezone);
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
    public function dateTime($timestamp, $newLine=false) {
        return $this->dateFormat($timestamp, $newLine?"m/d/y<b\\r/>g:i a":"m/d/y g:i a");
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
        $date = new DateTime(date('c', $timestamp));
        $date->setTimezone($this->timezone);
        return $date->format($formatString);
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

    public function number($val, $digits) {
        return sprintf("%.$digits" . "f", $val);
    }

    public function getTimeZoneName() {
        return $this->timezone->getName();
    }

    private static function getUTCTimeZone() {
        if (!self::$utcTimeZone) {
            self::$utcTimeZone = new DateTimeZone('UTC');
        }
        return self::$utcTimeZone;
    }
}
