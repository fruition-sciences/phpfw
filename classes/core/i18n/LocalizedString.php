<?php
/*
 * Created on Jul 14, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class LocalizedString {
    private $msg;
    private $attributes = array();

    public function setMessage($msg) {
        $this->msg = $msg;
    }

    public function set($key, $val) {
        $this->attributes[$key] = $val;
    }

    public function __toString() {
        return $this->msg;
    }
}