<?php
/*
 * Created on Jul 14, 2007
 * Author: Yoni Rosenbaum
 *
 */

class LocalizedString {
    private $msg;
    private $attributes = array();

    public function __construct($msg) {
        $this->setMessage($msg);
    }

    public function setMessage($msg) {
        $this->msg = $msg;
    }

    public function set($key, $val) {
        $this->attributes[$key] = $val;
        return $this;
    }

    public function get($key) {
        return $this->attributes[$key];
    }

    public function hasKey($key) {
        return isset($this->attributes[$key]);
    }

    public function __toString() {
        return $this->substituteVars();
    }

    /**
     * Substitute variables within the 'msg' with values of the 'attributes' map.
     * Variable exmaple: ${test}.
     *
     * @return String the message, where matching were replaced with their values.
     */
    private function substituteVars() {
        $resultMsg = $this->msg;
        foreach ($this->attributes as $key => $val) {
            if (is_object($val)) {
                // Skip this pair if value cannot be converted to string
                if (!method_exists($val, '__toString')) {
                    continue;
                }
                // Convert value to a string
                $val = $val->__toString();
            } 
            $search = '${'. $key . '}';
            $resultMsg = str_replace($search, $val, $resultMsg);
        }
        return $resultMsg;
    }
}