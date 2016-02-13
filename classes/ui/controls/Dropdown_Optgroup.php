<?php
/*
 * Created on Apr 30 2013
 * Author: Yoni Rosenbaum
 *
 */

class Dropdown_Optgroup extends Control {
    /**
     * @var Dropdown_Option[]
     */
    private $options = array();

    public function __construct($label, $form=null) {
        parent::__construct("optgroup");
        $this->set("label", $label);
        $this->setForm($form);
    }

    public function addOption($name, $value=null) {
        $option = new Dropdown_Option($name, $value);
        $option->setForm($this->getForm());
        $this->options[] = $option;
        return $this;
    }

    public function asString($values){
        $this->setBody($this->options_as_string($values));
        return parent::__toString();
    }

    public function toString() {
        return $this->get('label');
    }

    private function options_as_string($values) {
        $html = "";
        for ($i = 0; $i < sizeof($this->options); $i++) {
            $html .= $this->options[$i]->asString($values);
        }
        return $html;
    }

    /**
     * @return Dropdown_Option[]
     */
    public function getOptions(){
        return $this->options;
    }

    public function isEmpty() {
        return empty($this->options);
    }
}