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
     * @param int $datetype Date type to use (none, short, medium, long, full). See: http://www.php.net/manual/en/class.intldateformatter.php#intl.intldateformatter-constants
     * @param int $timetype Time type to use (none, short, medium, long, full). See: http://www.php.net/manual/en/class.intldateformatter.php#intl.intldateformatter-constants
     * @param String $pattern Optional pattern to use when formatting or parsing. Possible patterns are documented at http://userguide.icu-project.org/formatparse/datetime
     * @return long unix timestamp
     */
    public function parseDate($formattedDate, $datetype=IntlDateFormatter::SHORT, $timetype=IntlDateFormatter::SHORT, $pattern=null) {
        if (!$formattedDate) {
            return null;
        }
        
        $ftm = new IntlDateFormatter($this->locale, $datetype, $timetype, $this->timezoneName, null, $pattern);
        return $ftm->parse($formattedDate);
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