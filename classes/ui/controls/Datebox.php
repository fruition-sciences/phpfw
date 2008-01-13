<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Datebox extends HtmlElement {
    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "text");
        $this->set("id", $name);
        $this->set("size", 10);
    }

    public function toString() {
        $value = $this->getValue();
        return $value ? $value : "";
    }

    public function toInput() {
        $buttonName = $this->getButtonName();
        $img = new HtmlElement("img", $buttonName);
        $img->set('src', Application::getAppRoot() . "js/zpcal/themes/img.gif");
        $img->set('id', $buttonName);
        $img->set('class', 'calendarIcon');
        $script = $this->getCalInitScript();
        return parent::toInput() . $img . $script;
    }

    private function getCalInitScript() {
        $script = "\n<script type=\"text/javascript\">//<![CDATA[\n" .
            "Zapatec.Calendar.setup({" .
            "firstDay : 1, " .
            "weekNumbers : false, " .
            "electric : false, " .
            "inputField : \"" . $this->getName() . "\", " .
            "button : \"" . $this->getButtonName() . "\", " .
            "ifFormat : \"%m/%d/%Y\", " .
            "daFormat : \"%m/%d/%Y\"" .
            "});\n" .
            "//]]></script>\n";
        return $script;
    }

    private function getButtonName() {
        return $this->getName() . "_button";
    }
}