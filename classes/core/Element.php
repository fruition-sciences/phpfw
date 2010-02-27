<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Element {
    protected $atts = array();

    public function set($key, $val=null) {
        $this->atts[$key] = $val;
        return $this;
    }

    public function get($key) {
        return isset($this->atts[$key]) ? $this->atts[$key] : null;
    }

    public function un_set($key) {
        unset($this->atts[$key]);
        return $this;
    }

}