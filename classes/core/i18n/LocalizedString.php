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
            $search = '${'. $key . '}';
            $resultMsg = str_replace($search, $val, $resultMsg);
        }
        return $resultMsg;
    }
}