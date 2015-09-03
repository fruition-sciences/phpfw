<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Radio extends Control {
    private $value; // The 'run time' value of this radio. (not the 'value' property)
    
    public function __construct($name, $val) {
        parent::__construct("input", $name);
        $this->set("type", "radio");
        $this->setVal($val);
    }

    /**
     * Call this method to set the 'value' attribute for this control.
     * Normally, this is set by setValue(), however, for a checkbox, setValue()
     * will determine whether the checkbox is selected or not.
     *
     * @param String value the value to set
     */
    public function setVal($value) {
        return parent::setValue($value);
    }

    /**
     * This method is overriden so that it does not change the 'value' attribute
     * of this control, but instead, it changes whether the checkbox will be
     * selected or not, based on whether the value will be the same as the
     * 'value' attribute.
     * The 'value' attribute can be set using the setVal() method.
     *
     * @param String value the value
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function toInput() {
        if ($this->value == $this->getValue()) {
            $this->set("checked");
        }
        return parent::toInput();
    }

    public function toString() {
        $this->set('disabled');
        return $this->toInput();
    }
}