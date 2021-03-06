<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 *
 */

class HtmlElement extends Element {
    private $name;
    private $type;
    private $body;
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

    public function __toString()
    {
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
                $text .= "=\"" . htmlentities($value, ENT_COMPAT, "UTF-8") . "\"";
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

    /**
     * @return array
     */
    public function getCssClasses() {
        return $this->cssClasses;
    }
}
