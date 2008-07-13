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

    public function getLong($key) { 
        return $this->map[$key];
    }

    public function getDouble($key) { 
        return $this->map[$key];
    }

    public function getDate($key) {
        // Assumes that DB and PHP are both in UTC
        return strtotime($this->map[$key]);
    }

    public function containsKey($key) {
        return isset($this->map[$key]);
    }
}