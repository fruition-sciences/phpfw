<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Link extends HtmlElement {
    private $href;

    public function __construct($href, $title='') {
        parent::__construct("a");
        $this->href = $href;
        $this->setTitle($title);
    }

    public function setTitle($title) {
        $this->setBody($title);
        return $this;
    }

    public function setParam($name, $value) {
        $this->href->set($name, $value);
        return $this;
    }

    public function setAction($action) {
        $this->href->set("_ac", $action);
        return $this;
    }

    public function __toString() {
    	if ($this->href) {
    	    $this->set("href", Context::normalizePath($this->href->__toString()));
    	}
        return parent::__toString();
    }

    public function getHref() {
        return $this->href;
    }
}