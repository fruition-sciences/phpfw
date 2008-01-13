<?php
/*
 * Created on Jul 14, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class TextArea extends HTMLElement {
    public function __construct($name) {
        parent::__construct("textarea", $name);
        $this->set("cols", "40");
        $this->set("rows", "4");
    }

    public function setValue($value) {
        $this->setBody($value);
    }

    public function toString() {
        return htmlentities($this->getBody());
    }
}