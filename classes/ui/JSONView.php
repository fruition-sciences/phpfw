<?php
/*
 * Created on Feb 5, 2009
 * Author: Yoni Rosenbaum
 *
 */

class JSONView extends BaseView {
    private $doc; // php structure

    public function render($ctx) {
        echo json_encode($this->doc);
    }

    protected function setDoc($doc) {
        $this->doc = $doc;
    }

    protected function getDoc() {
        return $this->doc;
    }
}