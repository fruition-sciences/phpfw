<?php
/*
 * Created on Apr 30 2013
 * Author: bsoufflet
 *
 */

class Dropdown_Option extends Control {
    /**
     * @var Link
     */
    private $readonlyLink;

    /**
     * Construct a new dropdown option.
     *
     * @param String $label the text of this dropdown
     * @param String $value the value of this form element
     * @param Link $readonlyLink (optional) a link to render instead of the
     *        label, in readonly mode.
     */
    public function __construct($label, $value, $readonlyLink=null) {
        parent::__construct("option");
        $this->set("value", $value);
        $this->setBody($label);
        if ($readonlyLink) {
            $readonlyLink->setTitle($label);
            $this->readonlyLink = $readonlyLink;
        }
    }

    /**
     * String representation of this option, given the selected values.
     * If the value of this option is one of the given values, this option will
     * be rendered as selected.
     *
     * @param Array $values the values of the parent 'select' (or multi select).
     *        If this option's value is included in this array, the option will
     *        be rendered as selected.
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
        if ($this->readonlyLink !== null) {
            return $this->readonlyLink;
        }
        return $this->getBody();
    }
}