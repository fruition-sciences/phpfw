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

    /**
     * Check if the given key is defined in this template.
     * 
     * @param $key
     * @return true if the given key is defined in this template, even if its
     *         value is null.
     */
    public function containsKey($key) {
        return array_key_exists($key, $this->map) || isset($this->map[$key]);
    }
}