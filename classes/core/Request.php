<?php
/*
 * Created on Oct 7, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Request {
    const UNDEFINED = "___UNDEFINED___";
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

    public function getString($key, $defaultVal=self::UNDEFINED) {
        $map = $this->getAttributes();
        if (!isset($map[$key])) {
            if ($defaultVal === self::UNDEFINED) {
                throw new UndefinedKeyException('Missing argument: ' . $key);
            }
            else {
                return $defaultVal;
            }
        }
        return $map[$key];
    }

    public function getLong($key, $defaultValue=self::UNDEFINED) {
        $str = $this->getString($key, $defaultValue);
        return intval($str);
    }

    public function getBoolean($key, $defaultValue=self::UNDEFINED) {
        $str = $this->getString($key, $defaultValue);
        if ($str == "1") {
            return true;
        }
        return false;
    }

    /**
     * Get the date value associated with the given key.
     *
     * @param String $key the key
     * @param timestamp $dafaultValue value to return in case the key doesn't exist in the request.
     * @return timestamp Unix timestamp - the number of seconds since January 1 1970 00:00:00 GMT
     */
    public function getDate($key, $defaultValue=self::UNDEFINED) {
        try {
            $str = $this->getString($key);
            $converter = DataConverter::getInstance();
            return $converter->parseDate($str);
        }
        catch (UndefinedKeyException $e) {
            if ($defaultValue != self::UNDEFINED) {
                return $defaultValue;
            }
            throw $e;
        }
    }
}
