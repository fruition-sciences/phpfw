<?php
/*
 * Converts back and forth various object types into serialized values in a map.
 * The map is similar to the one used to keep Form values. The values are all
 * formatted strings. Certain data types are kept in more than a single key in
 * the map. (For example, measure if kept in two fields: value & unit).  
 *
 * The 'getter' methods convert from values in the map (formatted strings) to a
 * single value (or object).
 * The 'setter' methods convert from a single value and sets the proper key(s)
 * in the map.
 * 
 * Created on Feb 4, 2011
 * Author: Yoni Rosenbaum
 */

class InputConverter {
    private $timezoneName; // String
    private $locale;       // String
    private $formatter;

    public function __construct($timezoneName, $locale) {
        $this->timezoneName = $timezoneName;
        $this->locale = $locale;
        $this->formatter = new Formatter($timezoneName, $this->locale);
    }

    /**
     * Get a date (unix timestamp) from the given map.
     * 
     * Currently, parses the value associated with the key.
     * TODO: The map should contain 2 fields:
     * - $key : keeps the formatted date
     * - $key__time: keeps the formatted time 
     * 
     * @param $map
     * @param $key
     * @return unix timestamp
     */
    public function getDate($map, $key) {
        $value = $this->getValue($map, $key);

        // TODO: Parse according to locale
        $originalDefaulyTimezone = date_default_timezone_get();
        // Temporarily change time zone.
        date_default_timezone_set($this->timezoneName);

        $date = new DateTime($value);
        $tz = new DateTimeZone($this->timezoneName);
        $date->setTimezone($tz);

        // Set default time zone back
        date_default_timezone_set($originalDefaulyTimezone);
        return $date->format('U');
    }

    /**
     * Set the given date into the proper fields in the map.
     * Uses 2 fields:
     * - $key : keeps the formatted date
     * - $key__time: keeps the formatted time
     * 
     * NOTE: Currenty just set the formatted date
     *
     * @param $map
     * @param $key
     */
    public function setDate(&$map, $key, $value) {
        $map[$key] = $this->formatter->date($value);
    }

    /**
     * Get time value (number of seconds) from the given map.
     * The value in the map is assumed to be formatted as: "HH:MM:SS"
     * 
     * @param $map
     * @param $key
     * @return long number of seconds
     */
    public function getTime($map, $key) {
        $value = $this->getValue($map, $key);
        list($hours, $mins, $secs) = explode(':', $value);
        $seconds = $hours * 3600 + $mins * 60 + $secs;
        return $seconds;
    }

    /**
     * Sets the given number of seconds into the given map.
     * The value set will be formatted as relative time: "HH:MM:SS"
     * 
     * @param $map
     * @param $key
     * @param $value 
     */
    public function setTime(&$map, $key, $value) {
        $map[$key] = $this->formatter->secondsToTime();
    }

    /**
     * Get a measure object.
     * The measure object is constructed using the value and the unit should
     * be available in the map.
     * 
     * @param $map
     * @param $key
     * @return Zend_Measure_Abstract
     */
    public function getMeasure($map, $key) {
        $value = $this->getValue($map, $key);
        if (!$value) {
            return null;
        }
        $unitValue = $this->getValue($map, $key . '__unit');
        if (!$unitValue) {
            throw new IllegalArgumentException("The map should contain the unit");
        }
        return MeasureUtils::newMeasure($unitValue, $value);
    }

    /**
     * Set the given measure into the proper fields in the map.
     * Uses 2 fields:
     * - $key : keeps the value
     * - $key__unit : keeps the unit
     *
     * @param $map
     * @param $key
     * @param $measure
     */
    public function setMeasure(&$map, $key, $measure) {
        $this->setDouble($map, $key, $measure->getValue());
        $map[$key . '__unit'] = get_class($measure) . '::' . $measure->getType();
        $map[$key . '__measure'] = $measure;
    }

    public function getLong($map, $key) {
        $value = $this->getValue($map, $key);
        if (!$value) {
            return null;
        }
        return (int)$value; 
    }

    public function setLong(&$map, $key, $value) {
        // TODO: Format number
        $map[$key] = $value;
    }

    public function getDouble($map, $key) {
        $value = $this->getValue($map, $key);
        if (!$value) {
            return null;
        }
        return (float)$value; 
    }

    public function setDouble(&$map, $key, $value, $digits=null) {
        $map[$key] = isset($value) ? $this->formatter->number($value, $digits) : null;
    }

    public function getString($map, $key) {
        $value = $this->getValue($map, $key);
        if (!$value) {
            return null;
        }
        return (String)$value;
    }

    public function setString(&$map, $key, $value) {
        $map[$key] = $value;
    }

    public function getBoolean($map, $key) {
        $value = $this->getValue($map, $key);
        if (!$value) {
            return null;
        }
        return (bool)$value;
    }

    public function setBoolean(&$map, $key, $value) {
        $map[$key] = $value;
    }

    private function getValue($map, $key) {
        if (!$map[$key]) {
            return null;
        }
        return $map[$key];
    }
}