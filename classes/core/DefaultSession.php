<?php
/*
 * Created on Feb 19, 2008
 * Author: Yoni Rosenbaum
 * 
 * Default session implementation. Uses the PHP $_SESSION object.
 */

class DefaultSession implements Session {

    public function hasKey($key) {
        return isset($_SESSION[$key]);
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function un_set($key) {
        unset($_SESSION[$key]);
    }

    public function get($key, $defaultValue=null) {
        return $this->hasKey($key) ? $_SESSION[$key] : $defaultValue;
    }

    public function clear() {
        foreach ($_SESSION as $key=>$value) {
            $this->un_set($key);
        } 
    }
}