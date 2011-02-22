<?php
/*
 * Created on Oct 7, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Request {
    const UNDEFINED = "___UNDEFINED___";
    /**
     * @var Context
     */
    private $ctx;
    private $attributes;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    public function getAttributes() {
        if ($this->attributes === null) {
            $this->loadAttributes();
        }
        return $this->attributes;
    }

    /**
     * Read all attributes from the request.
     * Sets it into $this->attributes 
     * 
     * @return the attributes map
     */
    private function loadAttributes() {
        $this->attributes = array();
        $this->copyAttributes($_GET, $this->attributes, true);
        $this->copyAttributes($_POST, $this->attributes);

        // Add unchecked checkboxes to the map
        if (isset($_REQUEST['_checkboxes'])) {
            $names = explode(';', $_REQUEST['_checkboxes']);
            foreach ($names as $name) {
                // Ignore if name ends with []. This are being handled properly as array by PHP.
                if ($name != '' && !endsWith($name, '[]')) {
                    if (!isset($this->attributes[$name])) {
                        $this->attributes[$name] = "0";
                    }
                }
            }
        }
        return $this->attributes;
    }

    /**
     * Copy attributes from $sourceMap to $targetMap and (optionally) apply urldecoding.
     * Ignores certain keys, such as '_constraints' and '_checkboxes';
     * 
     * @param $sourceMap
     * @param $targetMap
     * @param $urlDecode if true, string values will be decoded
     */
    private function copyAttributes($sourceMap, &$targetMap, $urlDecode=false) {
        foreach ($sourceMap as $key=>$val) {
            if ($key != "_constraints" && $key != "_checkboxes") {
                if ($urlDecode && is_string($val)) {
                    $val = urldecode($val);
                }
                $targetMap[$key] = $val;
            }
        }        
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
        $val = $map[$key];
        if ($this->isGet() && is_string($val)) {
            $val = urldecode($val);
        }
        return $val;
    }

    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function isGet() {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
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
     * @param String $timezone If not passed, the user timezone will be used.
     * @return timestamp Unix timestamp - the number of seconds since January 1 1970 00:00:00 GMT
     */
    public function getDate($key, $defaultValue=self::UNDEFINED, $timezone=null) {
        try {
            $str = $this->getString($key);
            $converter = DataConverter::getInstance();
            if ($timezone && $converter->getTimeZoneName() != $timezone) {
                $converter = new DataConverter($timezone);
            }
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
