<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Dropdown extends HtmlElement {
    private $options = array();
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

    public function addOption($name, $value=null) {
        $option = new Dropdown_Option($name, $value);
        $option->setForm($this->getForm());
        $this->options[] = $option;
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
    	for ($i = 0; $i < sizeof($this->options); $i++) {
    		if ($this->options[$i]->get("value") == $this->value) {
    			if ($this->readonlyLink) {
    			    return $this->readonlyLink->setTitle($this->options[$i]->__toString())->__toString();
    			}
    			else {
    			    return $this->options[$i]->__toString();
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

class Dropdown_Option extends HtmlElement {
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
            $this->set('selected');
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