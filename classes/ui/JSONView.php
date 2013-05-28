<?php
/*
 * Created on Feb 5, 2009
 * Author: Yoni Rosenbaum
 *
 */

class JSONView extends BaseView {
    private $doc; // php structure
    
    public function __construct($doc) {
        $this->doc = $doc;
    }

    public function render($ctx) {
        header("Content-Type: application/json ; encoding=utf-8");
        echo json_encode($this->doc);
    }

    protected function setDoc($doc) {
        $this->doc = $doc;
    }

    protected function getDoc() {
        return $this->doc;
    }
}