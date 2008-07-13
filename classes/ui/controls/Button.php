<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("HtmlElement.php");

class Button extends HtmlElement {
    private $href;
    private $title;
    private $submit = true;
    private $onclick = array();

    public function __construct($title) {
        parent::__construct("button");
        $this->title = $title;
        $this->href = Href::current();
    }

    public function setUrl($url) {
        $this->href = new Href($url);
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

    public function confirm($msg) {
        $this->set("onclick", "if (!confirm('$msg')) return false");
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
        if ($this->submit) {
            $onclick = "button_submit('" . $this->href . "'); return false";
        }
        else {
            $onclick = "button_click('" . $this->href . "'); return false";
        }
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
}