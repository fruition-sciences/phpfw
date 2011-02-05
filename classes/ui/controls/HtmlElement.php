<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("classes/core/Element.php");

class HtmlElement extends Element {
    private $name;
    private $type;
    private $form;
    private $body;
    private $readonly; // if set, overwrites form's 'readonly' flag.
    private $cssClasses = array(); // Map (serves as a Set. Keys are css class names, values are 'true').

    public function __construct($type, $name='') {
        $this->type = $type;
        $this->name = $name;
        if ($name != '') {
            $this->set("name", $name);
        }
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    public function setValue($value) {
        $this->set("value", $value);
        return $this;
    }

    public function getValue() {
        return $this->get("value");
    }

    public function setForm($form) {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm() {
    	return $this->form;
    }

    /**
     * Set to null to use Form's 'readonly' flag. Or set to true/false to override.
     */
    public function setReadonly($readonly) {
        $this->readonly = $readonly;
    }

    /**
     * Get the 'readonly' flag.
     * Note: Does not check the Form's 'readonly' flag.
     *
     * @return Boolean the 'readonly' flag. null value means 'not set'.
     */
    public function isReadonly() {
        return $this->readonly;
    }

    public function __toString()
    {
        // Use 'readonly' field, if set
        if ($this->readonly !== null) {
            $readonly = $this->readonly;
        }
        else {
            // Otherwise, use 'readonly' flag from form, or false if there is no form.
            $readonly = isset($this->form) ? $this->form->isReadonly() : false;
        }
        if ($readonly) {
            return (string)$this->toString();
        }
        else {
            return $this->toInput();
        }
    }

    public function toString() {
        $value = $this->getValue();
        return $value != null ? $value : "";
    }

    public function toInput() {
        $html = self::getElementOpenTag();
        $html .= $this->body;
        $html .= self::getElementCloseTag();
        return $html;
    }

    public function getElementOpenTag() {
        $html = "<" . $this->type . " ";
        $html .= $this->getAttributesAsString();
        $html .= $this->getCssClassNamesAsString();
        $html .= ">";
        return $html;
    }

    private function getAttributesAsString() {
        $text = "";
        foreach ($this->atts as $key=>$value) {
            if ($text) {
                $text .= " ";
            }
            $text .= $key;
            if (isset($value)) {
                $text .= "=\"" . htmlentities($value) . "\"";
            }
        }
        return $text;
    }

    private function getCssClassNamesAsString() {
        if (!$this->cssClasses) {
            return '';
        }
        $cssClasses = array_keys($this->cssClasses);
        return ' class="' . implode(' ', $cssClasses) . '"';
    }

    public function getElementCloseTag() {
        return "</" . $this->type . ">";
    }

    public function getName() {
        return $this->name;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function confirm($msg) {
        $text = str_replace("'", "\\'", (string)$msg);
    	$this->set("onclick", "if (!confirm('$text')) return false");
        return $this;
    }

    public function setTooltip($text) {
        $this->set('title', $text);
        return $this;
    }

    /**
     * Set the css class of this element.
     * 
     * @param $cssClass
     * @return HtmlElement
     */
    public function setClass($cssClass) {
        if ($this->cssClasses) {
            $this->cssClasses = array();
        }
        $this->addClass($cssClass);
        return $this;
    }

    /**
     * Add a css class to this element.
     * @param $cssClass
     * @return HtmlElement
     */
    public function addClass($cssClass) {
        $this->cssClasses[$cssClass] = true;
        return $this;
    }

    /**
     * Overridden to handle key=class.
     * 
     * (non-PHPdoc)
     * @see classes/core/Element#set($key, $val)
     */
    public function set($key, $val=null) {
        if (strtolower($key) == 'class') {
            return $this->setClass($val);
        }
        // In all other cases
        return parent::set($key, $val);
    }
}
