<?php
/*
 * Created on Oct 12, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Hidden extends HtmlElement {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "hidden");
    }
    /**
     * An hidden field will not be display as text in readonly mode.
     * @see classes/ui/controls/HtmlElement#toString()
     */
    public function toString() {
        return "";
    }
}