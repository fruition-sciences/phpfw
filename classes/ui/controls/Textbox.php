<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("HtmlElement.php");

class Textbox extends Control {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "text");
    }
}