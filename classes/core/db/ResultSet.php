<?php
/*
 * Created on Oct 12, 2007
 * Author: Yoni Rosenbaum
 *
 */

class ResultSet {
    private $map;

    public function __construct($map) {
        $this->map = $map;
    }

    public function getString($key) {
        return $this->map[$key];
    }

    public function setString($key, $value) {
        $this->map[$key] = $value;
    }

    public function getLong($key) {
        $val = $this->map[$key];
        return ($val === null) ? null : (int)$val;
    }

    /**
     * Like getLong() but translate null to -1.
     * 
     * @param $key
     * @return long
     */
    public function getId($key) {
        $value = $this->map[$key];
        if ($value === null) {
            return -1;
        }
        return (int)$value;
    }

    public function setLong($key, $value) {
        $this->map[$key] = $value;
    }

    public function getDouble($key) {
        $val = $this->map[$key];
        return ($val === null) ? null : (float)$val;
    }

    public function setDouble($key, $value) {
        $this->map[$key] = $value;
    }

    public function getDate($key, $timeZone='GMT') {
        $converter = new DataConverter($timeZone);
        return $converter->parseDate($this->map[$key], null, null, 'yyyy-MM-dd hh:mm:ss');
    }

    public function setDate($key, $value) {
        // Assumes that DB and PHP are both in UTC
        $this->map[$key] = $value;
    }
    
    public function getTime($key) {
        return DataConverter::parseTime($this->map[$key]);
    }

    public function setTime($key, $value) {
        $this->map[$key] = $value;
    }

    /**
     * Return a GeomPoint representing the value associated with the given key.
     * The value associated with the key is assumed to be a string in WKT format.
     * 
     * @param $key
     * @return GeomPoint or null if the value associated with the given key is null.
     */
    public function getPoint($key) {
        $val = $this->map[$key];
        return $val === null ? null : GeomPoint::fromWKT($val); 
    }

    /**
     * Return a GeomPolygon if block geometry is specified
     * Otherwise return null
     * 
     * @param $key
     * @return GeomPolygon or null if the value associated with the given key is null.
     */
    public function getPolygon($key) {
        $val = $this->map[$key];
        return $val === null ? null : new GeomPolygon($val);
    }

    public function containsKey($key) {
        return isset($this->map[$key]);
    }

    /**
     * Return the entire map of attributes.
     *
     * @return Map the attributes
     */
    public function getAttributes() {
        return $this->map;
    }
}
