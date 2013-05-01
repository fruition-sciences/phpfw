<?php
/*
 * Created on Apr 30 2013
 * Author: bsoufflet
 *
 */

class NumberConstraintLimit {
    private $value;
    private $exclusive;

    public function __construct($value, $exclusive=false) {
        if (!is_numeric($value)) {
            throw new IllegalArgumentException("value must be a number");
        }
        $this->value = $value;
        $this->exclusive = $exclusive;
    }

    public function getValue() {
        return $this->value;
    }

    public function isExclusive() {
        return $this->exclusive;
    }
}