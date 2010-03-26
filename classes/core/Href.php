<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("Element.php");

class Href extends Element {
    private $path; // The url, without the parameters

    public function __construct($path='') {
        $this->parse($path);
    }

    public static function current() {
        $pathInfo = '/' . Application::getPathInfo();
        $href = self::from_url($pathInfo);
        return $href;
    }

    public static function from_url($url) {
        $href = new Href($url);
        if (isset($_SERVER['QUERY_STRING'])) {
            $href->setQuery($_SERVER['QUERY_STRING']);
        }
        $href->un_set('_ac');
        return $href;
    }

    /**
     * Set all the parameters from the given query string into this Href.
     * All prior parameters are removed.
     * 
     * @param String query new query string to apply.
     */
    public function setQuery($query) {
        $this->removeAll();
        $pairs = explode("&", $query);
        for ($i = 0; $i < sizeof($pairs); $i++) {
            $pair = $pairs[$i];
            $nameVal = explode("=", $pair);
            if (sizeof($nameVal) == 2) {
                $this->set($nameVal[0], $nameVal[1], true);
            }
        }
    }

    /**
     * Serialize this Href as a URL string.
     * String values are being urlencoded.
     * Array values get translatetd to multiple entries with the same key.
     * '[]' is added to array keys if it's not there already.
     * 
     * @return String
     */
    public function __toString() {
        $text = $this->path;
        $nameValueList = array();
        foreach ($this->atts as $key=>$value) {
            if (is_array($value)) {
                if (!endsWith($key, '[]')) {
                    $key .= '[]';
                }
                foreach ($value as $entry) {
                    $nameValueList[] = $this->getQueryStringNameValue($key, $entry);
                }
            }
            else {
                $nameValueList[] = $this->getQueryStringNameValue($key, $value);
            }
        }
        if ($nameValueList) {
            $text .= "?";
            $text .= implode('&', $nameValueList);
        }
        return $text;
    }

    private function getQueryStringNameValue($key, $value) {
        if (is_string($value)) {
            $value = urlencode($value);
        }
        return "$key=$value";
    }

    /**
     * Set value to the given key.
     * If $addToArray is true, existing keys will not be overwritten, rather,
     * if the new value is already in the map, it will be added into an array.
     * 
     * @param $key
     * @param $val
     * @param $addToArray
     */
    public function set($key, $val, $addToArray=false) {
        // Support 'addToArray', if new value is not an array. 
        if ($addToArray && !is_array($val)) {
            $existingVal = $this->get($key);
            // If there is an existing value
            if ($existingVal !== null) {
                // Turn existing value into an array (if it's not already)
                if (!is_array($existingVal)) {
                    $existingVal = array($existingVal);
                }
                // Add new value to array
                $existingVal[] = $val;
                // Set the array into '$val' and proceed normally
                $val = $existingVal;
            }
        }
        return parent::set($key, $val);
    }

    public function getAttributes() {
        return $this->atts;
    }

    private function parse($path) {
        $parts = explode('?', $path);
        $this->path = $parts[0];
        if (count($parts) == 1) {
            return;
        }
        $this->setQuery($parts[1]);
    }
}