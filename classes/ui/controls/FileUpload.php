<?php
/*
 * Created on Dec 7, 2007
 * Author: Yoni Rosenbaum
 */

require_once("HtmlElement.php");

class FileUpload extends HtmlElement {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "file");
    }
}