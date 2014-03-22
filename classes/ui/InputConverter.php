<?php
/*
 * Converts back and forth various object types into serialized values in a map.
 * The map is similar to the one used to keep Form values. The values are all
 * formatted strings. Certain data types are kept in more than a single key in
 * the map. (For example, measure is kept in two fields: value & unit).  
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
     * Parses the date using the Zend_Date::DATETIME_SHORT format, which accepts
     * year as either 2 or 4 digits.
     * 
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
        if (!isset($value) || $value === "" || $value === false) {
            return null;
        }
        $dataConverter = new DataConverter($this->timezoneName, $this->locale);
        return $dataConverter->parseDate($value, Zend_Date::DATETIME_SHORT);
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

    public function setDateTime(&$map, $key, $value) {
        $map[$key] = $this->formatter->dateTime24($value);
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
        if (!isset($value) || $value === "" || $value === false) {
            return null;
        }
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
        $map[$key] = $this->formatter->secondsToTime($value);
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
        if (!isset($value) || $value === "" || $value === false) {
            return null;
        }
        $unitValue = $this->getValue($map, $key . '__unit');
        if (!$unitValue) {
            throw new IllegalArgumentException("The map should contain the unit");
        }
        return MeasureUtils::newMeasure($unitValue, $this->formatter->getNumber($value));
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
    
    public function getId($map, $key) {
        $value = $this->getValue($map, $key);
        if (!$value) {
            return null;
        }
        return (int)$value; 
    }

    public function setId(&$map, $key, $value) {
        $map[$key] = $value;
    }

    public function getLong($map, $key) {
        $value = $this->getValue($map, $key);
        if (!isset($value) || $value === "" || $value === false) {
            return null;
        }
        return (int)$this->formatter->getNumber($value, 0); 
    }

    public function setLong(&$map, $key, $value) {
        $map[$key] = isset($value) ? $this->formatter->number($value, 0) : null;
    }

    public function getDouble($map, $key) {
        $value = $this->getValue($map, $key);
        if (!isset($value) || $value === "" || $value === false) {
            return null;
        }
        return (float)$this->formatter->getNumber($value); 
    }

    public function setDouble(&$map, $key, $value, $digits=null) {
        $map[$key] = isset($value) ? $this->formatter->number($value, $digits) : null;
    }

    public function getString($map, $key) {
        $value = $this->getValue($map, $key);
        if (!isset($value) || $value === "" || $value === false) {
            return null;
        }
        return (String)$value;
    }

    public function setString(&$map, $key, $value) {
        $map[$key] = $value;
    }

    public function getBoolean($map, $key) {
        $value = $this->getValue($map, $key);
        if (!isset($value) || $value === "") {
            return null;
        }
        return (bool)$value;
    }

    public function setBoolean(&$map, $key, $value) {
        $map[$key] = $value;
    }

    /** 
     * Set longitude and latitude values into the map
     * 
     * @param $map
     * @param $key
     * @param GeomPoint $geomPoint
     */
    public function setPoint(&$map, $key, $geomPoint) {
        if ($geomPoint) {
            $map[$key . "_X"] = $geomPoint->getX();
            $map[$key . "_Y"] = $geomPoint->getY();
        }    
        $map[$key] = $geomPoint;
    }
    
    /** 
     * Create a string (with WKT format) from longitude and latitude
     * WKT format for point type: POINT(X Y)
     *  
     * @return GeomPoint|null if values are not set properly in the map.
     */
    public function getPoint($map, $key) {
        $x = $this->getValue($map, $key . '_X');
        $y = $this->getValue($map, $key . '_Y');
        if (!is_numeric($x) && !is_numeric($y)) {
            return null;
        }
        return GeomPoint::fromXY($x, $y);;
    }
    
    /**
     * @param $map
     * @param $key
     * @param GeomPolygon $geomPolygon
     */
    public function setPolygon(&$map, $key, $geomPolygon) {
        $map[$key] = $geomPolygon ? $geomPolygon->toWKT() : null;
    }
    
    /**
     * 
     * Return a GeomPoint if the Block Geometry is specified in the map
     * Otherwise return null
     * 
     * @param $map
     * @param $key
     * @return GeomPolygon|null
     */
    public function getPolygon($map, $key) {
        $wkt = $this->getValue($map, $key);
        if (!empty($wkt)) {
            return new GeomPolygon($wkt);
        }
        return null;
    }

    private function getValue($map, $key) {
        if (!isset($map[$key])) {
            return null;
        }
        return $map[$key];
    }
}