<?php
/*
 * Created on Oct 7, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Request {
    private $ctx;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    public function getAttributes() {
        $map = array();
        foreach ($_REQUEST as $key=>$val) {
        	if ($key != "_constraints" && $key != "_checkboxes") {
        		$map[$key] = $val;
        	}
        }
        // Add unchecked checkboxes to the map
        if (isset($_REQUEST['_checkboxes'])) {
        	$names = split(';', $_REQUEST['_checkboxes']);
            foreach ($names as $name) {
                if ($name != '') {
            		if (!isset($map[$name])) {
                        $map[$name] = "0";
                    }
            	}
            }
        }
        return $map;
    }

    public function containsKey($key) {
        return isset($_REQUEST[$key]);
    }

    public function getString($key, $defaultVal=null) {
        if (!$this->containsKey($key)) {
            if (!isset($defaultVal)) {
                throw new Exception('Undefined Request key: ' . $key);
            }
            else {
                return $defaultVal;
            }
        }
        return $_REQUEST[$key];
    }

    public function getLong($key, $defaultValue=null) {
        $str = $this->getString($key, $defaultValue);
        return intval($str);
    }

    public function getBoolean($key, $defaultValue=null) {
        $str = $this->getString($key, $defaultValue);
        if ($str == "1") {
            return true;
        }
        return false;
    }
}