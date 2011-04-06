<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Dropdown extends Control {
    private $options = array();
    private $optgroups = array();
    private $value;
    private $readonlyLink;

    public function __construct($name) {
        parent::__construct("select", $name);
    }

    /**
     * @deprecated use addOption
     */
    public function add_option($name, $value=null) {
        return $this->addOption($name, $value);
    }

    public function addOption($name, $value=null, $tooltip=null) {
        $option = new Dropdown_Option($name, $value);
        if ($tooltip) {
            $option->set("title", $tooltip);
        }
        $option->setForm($this->getForm());
        $this->options[] = $option;
        return $this;
    }
    
    public function addOptgroup($label) {
        $optgroup = new Dropdown_Optgroup($label);
        $optgroup->setForm($this->getForm());
        return $this->addOptgroupObject($optgroup);
    }
    
    public function addOptgroupObject($optgroup) {
        $optgroup->setForm($this->getForm());
        $this->optgroups[] = $optgroup;
        return $this;
    }
    

    /**
     * Set a link to be shown instead of the regular title in readonly mode.
     * The title to this link will be set as the option name.
     *
     * @param Link $readonlyLink the link to show in readonly mode.
     */
    public function setReadonlyLink($readonlyLink) {
        $this->readonlyLink = $readonlyLink;
    }

    public function __toString()
    {
        $this->setBody($this->options_as_string());
        return parent::__toString();
    }

    public function toString() {
        $options = $this->options;
        foreach ($this->optgroups as $optgroup) {
            $options = array_merge($options, $optgroup->getOptions());
        }
        foreach($options as $option) {
            if ($option->get("value") == $this->value) {
                if ($this->readonlyLink) {
                    return $this->readonlyLink->setTitle($option->__toString())->__toString();
                }
                else {
                    return $option->__toString();
                }
            }
        }
        return "";
    }

    private function options_as_string() {
        $html = "";
        for ($i = 0; $i < sizeof($this->options); $i++) {
            $html .= $this->options[$i]->asString($this->value);
        }
        for ($i = 0; $i < sizeof($this->optgroups); $i++) {
            $html .= $this->optgroups[$i]->asString($this->value);
        }
        return $html;
    }

    /**
     * Overriden. Instead of setting the HTML value attribute, keep it as a
     * private. It will be used in order to mark the 'option' who's got this
     * value as selected.
     *
     * @param String $value the value.
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }
}

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
     * @param String $value the value of the containing 'select'. If it's equal
     *        to the value of this option, the option will be marked as selected.
     */
    public function asString($value) {
        $v = $this->get('value');
        if ($v == $value) {
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

class Dropdown_Optgroup extends Control {
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
    
    public function asString($value){
        $this->setBody($this->options_as_string($value));
        return parent::__toString();
    }

    public function toString() {
        return $this->get('label');
    }
    
    private function options_as_string($value) {
        $html = "";
        for ($i = 0; $i < sizeof($this->options); $i++) {
            $html .= $this->options[$i]->asString($value);
        }
        return $html;
    }
    public function getOptions(){
        return $this->options;
    }
}