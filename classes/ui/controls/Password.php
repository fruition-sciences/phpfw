<?php
/*
 * Created on Aug 31, 2007
 * Author: Yoni Rosenbaum
 */

class Password extends Control {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "password");
    }
}
