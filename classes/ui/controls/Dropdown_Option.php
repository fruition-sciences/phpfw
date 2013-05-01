<?php
/*
 * Created on Apr 30 2013
 * Author: bsoufflet
 *
 */

class Dropdown_Option extends Control {
    public function __construct($label, $value) {
        parent::__construct("option");
        $this->set("value", $value);
        $this->setBody($label);
    }

    /**
     * String representation of this option, given a value. If the value is the
     * same as this option's value then this option will be selected.
     *
     * @param Array $values the value(s) of the containing 'select' (or multi select).
     * If it's equal to the value of this option, the option will be marked as selected.
     */
    public function asString($values) {
        $v = $this->get('value');
        if (in_array($v, $values)) {
            $this->set('selected','selected');
        }
        else {
            $this->un_set('selected');
        }
        return $this->__toString();
    }

    public function toString() {
        return $this->getBody();
    }
}