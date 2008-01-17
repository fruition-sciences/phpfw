<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Template {
    private $map = array();
    private $page;

    public function __construct($page) {
        $this->page = $page;
    }

    public function set($key, $val) {
        $this->map[$key] = $val;
    }

    public function get($key) {
        return $this->map[$key];
    }

    public function containsKey($key) {
        return isset($this->map[$key]);
    }
}