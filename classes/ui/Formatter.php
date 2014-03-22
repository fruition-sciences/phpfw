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
     * @deprecated most likely will be removed
     */
    private $timezone;

    /**
     * @var String
     */
    private $timezoneName;

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
     * @param String $localeName For example: "en_US".
     */
    public function Formatter($timezoneName, $localeName=null) {
        if (!$localeName) {
            $localeName = Transaction::getInstance()->getUser()->getLocale();
        }
        $this->timezoneName = $timezoneName;
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
     * @param String $default value to return if the given timestamp is null
     * @return String formatted date, of the given timestamp in the timezone set
     *         for this Formatter object.
     */
    public function date($timestamp, $default='') {
        return $this->zendDate($timestamp, Zend_Date::DATE_SHORT, $default);
    }

    /**
     * Format the given timestamp as date & time.
     *
     * @return String formatted date and time, of the given timestamp in the
     *         timezone set for this Formatter object.
     */
    public function dateTime($timestamp, $newLine=false, $showSeconds=false) {
        return $this->zendDate($timestamp, Zend_Date::DATETIME_SHORT, '');
    }

    /**
     * @deprecated not locale-aware
     * @param unknown_type $timestamp
     */
    public function dateTime24($timestamp) {
        return $this->dateFormat($timestamp, "m/d/Y H:i:s");
    }

    /**
     * Format the given timestamp as time.
     * 
     * @param long $timestamp
     * @param boolean $showSeconds whether seconds should be included.
     * @return String formatted time, of the given timestamp in the
     *         timezone set for this Formatter object.
     */
    public function time($timestamp, $showSeconds=false) {
        $zendFormat = $showSeconds ? Zend_Date::TIME_MEDIUM : Zend_Date::TIME_SHORT;
        return $this->zendDate($timestamp, $zendFormat, '');
    }

    /**
     * Format the given timestamp using the given zend format and using the
     * locale defined in this formatter.
     * If the given timestamp is null, returns an empty string.
     * @see Zend_Date
     *
     * @param long $timestamp
     * @param String $zendFormat
     * @param String $default value to return if the given timestamp is null
     */
    private function zendDate($timestamp, $zendFormat, $default) {
        if (!$timestamp) {
            return $default;
        }
        $zendDate = new Zend_Date($timestamp, Zend_Date::TIMESTAMP, $this->zendLocale);
        $zendDate->setTimezone($this->timezoneName);
        return $zendDate->toString($zendFormat);
    }

    public function secondsToTime($seconds) {
        $timeStr = substr(SQLUtils::convertTime($seconds), 1, -1);
        return $timeStr == "null" ? "" : $timeStr;
    }

    /**
     * Format the given timestamp using the given format string.
     * Note: This method is not locale aware
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

    /**
     * Format a string or a number (float or int) to the Formatter
     * locale with the $digits precision.
     * @param String $val Value to format
     * @param Integer $digits Precision
     */
    public function number($val, $digits=null) {
        return Zend_Locale_Format::toNumber(
            $val,
            array('locale' => $this->zendLocale,
                  'precision' => $digits));
    }
    
    /**
     * Returns the normalized number from a localized one
     * Parsing depends on the Formatter locale
     * @param String $input Formatted String
     * @param Integer $digits Precision
     * @return String normalized number of Boolean false
     * if $input is not a valid formatted number.
     */
    public function getNumber($input, $digits=null){
        try {
            return Zend_Locale_Format::getNumber(
                $input,
                array('locale' => $this->zendLocale,
                      'precision' => $digits));
        } catch(Zend_Locale_Exception $e) {
            // The string is not a valid formatted number.
            return false;
        }
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

    /**
     * @return Zend_Locale
     */
    public function getLocale() {
        return $this->zendLocale;
    }

    public function getLocaleName() {
        return $this->zendLocale->toString();        
    }
}