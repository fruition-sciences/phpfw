<?php
/*
 * Created on Aug 31, 2007
 * Author: Yoni Rosenbaum
 */

require_once("HtmlElement.php");

class Password extends Control {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "password");
    }
}
