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
        return (int)$this->map[$key];
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
        return $this->map[$key];
    }

    public function setDouble($key, $value) {
        $this->map[$key] = $value;
    }

    public function getDate($key, $timeZone='GMT') {
        $converter = new DataConverter($timeZone);
        return $converter->parseDate($this->map[$key]);
    }
    public function setDate($key, $value) {
        // Assumes that DB and PHP are both in UTC
        $this->map[$key] = $value;
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
