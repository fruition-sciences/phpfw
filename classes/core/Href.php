<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("Element.php");

class Href extends Element {
    private $path;

    public function __construct($path='') {
        $this->path = $path;
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
     * New parameters will be added. Existing ones will be overwritten.
     * 
     * @param String query new query string to apply.
     */
    public function setQuery($query) {
        $pairs = split("&", $query);
        for ($i = 0; $i < sizeof($pairs); $i++) {
        	$pair = $pairs[$i];
        	$nameVal = split("=", $pair);
        	if (sizeof($nameVal) == 2) {
        	    // Call set on parent, so values are not encoded (they are already encoded)
        	    parent::set($nameVal[0], $nameVal[1]);
        	}
        }
    }

    public function __toString()
    { 
        $text = $this->path;
        $sep = "?";
        foreach ($this->atts as $key=>$value) {
            $text .= $sep . $key . "=" . $value;
            $sep = "&";
        }
        return $text;
    }

    public function set($key, $val) {
        return parent::set($key, urlencode($val));
    }

    public function get($key) {
        return urldecode(parent::get($key));
    }
}