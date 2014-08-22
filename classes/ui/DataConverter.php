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
     * If $pattern is given, parsing will be strictly by this pattern.
     * Otherwise, will try various localized patterns. Currently tries datetime
     * and then date.
     * 
     * @param String $formattedDate formatted date (or date & time)
     * @param String $pattern Optional pattern to use when formatting or parsing. 
     *               Patterns are documented at http://userguide.icu-project.org/formatparse/datetime
     * @return long unix timestamp
     */
    public function parseDate($formattedDate, $pattern=null) {
        if (!$formattedDate) {
            return null;
        }

        if ($pattern) {
            return $this->parseDateByPattern($formattedDate, $pattern);
        }

        // First try parsing as datetime
        $pattern = DataConverter::getDatePattern($this->locale, true, true);
        $timestamp = $this->parseDateByPattern($formattedDate, $pattern);
        if (is_numeric($timestamp)) {
            return $timestamp;
        }

        // If parsing failed, try parsing as date only
        $pattern = DataConverter::getDatePattern($this->locale, true, false);
        return $this->parseDateByPattern($formattedDate, $pattern);
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

    /**
     * Get the pattern to be used to format and parse date/time.
     * The pattern is locale specific.
     * The returned pattern is according to this specification: http://userguide.icu-project.org/formatparse/datetime
     * 
     * Note:
     * 1. The $locale parameter is here becuase it *should* be used, however
     *    current implementation uses Application::getTranslator(), which is
     *    static so locale is not being use. It should be changed to use the given locale.
     * 2. Calls to the translator should explicitly send the literal pattern so
     *    that it is picked by the translator parser. (poedit).
     * 
     * @param String $locale
     * @param boolean $withDate whether the pattern should include date. Default is true.
     * @param boolean $withTime whether the pattern should include time. Default is false.
     * @param boolean $timeIncludesSeconds whether the time should include seconds. Default is false.
     *        Relevant only if $withTime is true.
     *        Note: Currently ignored if $withDate is true.
     * @return String
     */
    public static function getDatePattern($locale, $withDate=true, $withTime=false, $timeIncludesSeconds=false) {
        if (!$withDate && !$withTime) {
            throw new IllegalArgumentException("Date format must contain date or/and time");
        }
        $translator = Application::getTranslator();
        if ($withDate && $withTime) {
            return $translator->_('M/dd/yyyy h:mm a');
        }
        if ($withDate) {
            return $translator->_('M/dd/yyyy');
        }
        if ($withTime) {
            return $timeIncludesSeconds ? $translator->_('h:mm:ss a') : $translator->_('h:mm a');
        }
    }

    private function parseDateByPattern($formattedDate, $pattern) {
        $ftm = new IntlDateFormatter($this->locale, null, null, $this->timezoneName, null, $pattern);
        return $ftm->parse($formattedDate);
    }
}