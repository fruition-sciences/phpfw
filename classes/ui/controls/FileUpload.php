<?php
/*
 * Created on Dec 7, 2007
 * Author: Yoni Rosenbaum
 */

class FileUpload extends Control {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "file");
    }
}