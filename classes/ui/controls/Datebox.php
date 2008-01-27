<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Datebox extends HtmlElement {
    private static $dateFormat = "%m/%d/%Y";
    private static $dateTimeFormat = "%m/%d/%Y  %I:%M %P";

    private $showTime = false;

    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "text");
        $this->set("id", $name);
        $this->set("size", 10);
    }

    public function showTime() {
        $this->showTime = true;
        $this->set("size", 18);
        return $this;
    }

    private function getDateFormat() {
        return $this->showTime ? self::$dateTimeFormat : self::$dateFormat;
    }

    public function toString() {
        $value = $this->getValue();
        return $value ? $value : "";
    }

    public function toInput() {
        $buttonName = $this->getButtonName();
        $img = new HtmlElement("img", $buttonName);
        $img->set('src', Application::getAppRoot() . "js/core/zpcal/themes/img.gif");
        $img->set('id', $buttonName);
        $img->set('class', 'calendarIcon');
        $script = $this->getCalInitScript();
        return parent::toInput() . "&nbsp;" . $img . $script;
    }

    private function getCalInitScript() {
        $script = "\n<script type=\"text/javascript\">//<![CDATA[\n" .
            "Zapatec.Calendar.setup({" .
              "firstDay : 1, " .
              "weekNumbers : false, " .
              "showsTime   : " . self::booleanToString($this->showTime) . "," .
              "electric : false, " .
              "inputField : \"" . $this->getName() . "\", " .
              "button : \"" . $this->getButtonName() . "\", " .
              "ifFormat : \"" . $this->getDateFormat() . "\", " .
              "daFormat : \"%m/%d/%Y\"" .
            "});\n" .
            "//]]></script>\n";
        return $script;
    }

    private function getButtonName() {
        return $this->getName() . "_button";
    }

    private static function booleanToString($val) {
        return $val ? "true" : "false";
    }
}