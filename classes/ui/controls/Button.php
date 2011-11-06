<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("HtmlElement.php");

class Button extends Control {
    private $href;
    private $title;
    private $target; // Optional
    private $submit = true;
    private $onclick = array();

    public function __construct($title) {
        parent::__construct("button");
        $this->title = $title;
        $this->href = Href::current();
    }

    public function setUrl($url) {
        if (is_object($url) && get_class($url) == 'Href') {
            $this->href = $url;
        }
        else {
            $this->href = new Href($url);
        }
        return $this;
    }

    public function setAction($action) {
        $this->href->set("_ac", $action);
        return $this;
    }

    public function noSubmit() {
        $this->submit = false;
        return $this;
    }

    /**
     * Override to deal with known events, such as 'onclick', so that they are
     * concatenated and not overwritten.
     *
     * @param String $key
     * @param String $val
     */
    public function set($key, $val) {
        if ($key == "onclick") {
            $this->onclick[] = $val;
        }
        else {
            parent::set($key, $val);
        }
        return $this;
    }

    public function setParam($name, $value) {
        $this->href->set($name, $value);
        return $this;
    }

    public function unsetParam($name) {
        $this->href->un_set($name);
        return $this;
    }

    public function __toString()
    {
        $this->setOnClick();
        $this->setBody($this->title);
        if ($this->getType() != 'button') {
            $this->set('button', '1');
        }
        return parent::__toString();
    }

    private function setOnClick() {
        $params = array($this->href);
        if ($this->target) {
            $params[] = $this->target;
        }
        // Surround params with single quotes 
        $params = array_map(function($str) {
            return "'" . $str . "'";
        }, $params);

        $paramListString = implode(', ', $params);
        $jsFunctionName = $this->submit ? 'button_submit' : 'button_click';
        $onclick = "$jsFunctionName($paramListString); return false"; 
        $this->onclick[] = $onclick;
        $onclickStr = StringUtils::arrayToString($this->onclick, "; ", true);
        parent::set("onclick", $onclickStr);
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    /**
     * Allows opening the URL in a new window.
     * 
     * @param String $target the name of the new window.
     */
    public function setTarget($target) {
        $this->target = $target;
        return $this;
    }
}
