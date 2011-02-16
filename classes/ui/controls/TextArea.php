<?php
/*
 * Created on Jul 14, 2007
 * Author: Yoni Rosenbaum
 *
 */

class TextArea extends Control {
    public function __construct($name) {
        parent::__construct("textarea", $name);
        $this->set("cols", "40");
        $this->set("rows", "4");
    }

    public function setValue($value) {
        $this->setBody($value);
        return $this;
    }

    public function toString() {
        return str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",nl2br(htmlentities($this->getBody())));
    }
}