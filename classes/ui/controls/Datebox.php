<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Datebox extends HtmlElement {
    private static $dateFormat = "%m/%d/%y";
    private static $timeFormat = "%l:%M %P";
    private static $dateTimeFormat = "%m/%d/%y %l:%M %P";
    
    private static $dateFormatLabel = "MM/DD/YY";
    private static $timeFormatLabel = "HH:MM pm";
    private static $dateTimeFormatLabel = "MM/DD/YY HH:MM pm";

    private $showTime = false;
    private $showDate = true;
    private $showPopup = true;
    private $showMinute = false;

    public function __construct($name) {
        parent::__construct("input", $name);
        $this->set("type", "text");
        $this->set("id", $name);
        $this->set("class", "date");
    }

    public function showTime() {
        $this->showTime = true;
        $this->set("class", "dateTime");
        return $this;
    }
    
    public function showTimeOnly() {
        $this->showTime = true;
        $this->showDate = false;
        $this->showPopup = false;
        $this->set("class", "time");
        return $this;
    }
    
    public function showMinute() {
        $this->showMinute = true;
        return $this;
    }
    
    public function noPopup(){
        $this->showPopup = false;
    }

    private function getDateFormat() {
        if($this->showTime && $this->showDate)
            return self::$dateTimeFormat;
        if($this->showTime)
            return self::$timeFormat;
        return self::$dateFormat;
    }
    
    private function getDateFormatLabel() {
        if($this->showTime && $this->showDate)
            return self::$dateTimeFormatLabel;
        if($this->showTime)
            return self::$timeFormatLabel;
        return self::$dateFormatLabel;
    }

    public function toString() {
        $value = $this->getValue();
        return $value ? $value : "";
    }

    public function toInput() {
        if($this->showPopup){
            $buttonName = $this->getButtonName();
            $img = new HtmlElement("img", $buttonName);
            $img->set('src', Application::getAppRoot() . "js/core/zpcal/themes/img.gif");
            $img->set('id', $buttonName);
            $img->set('class', 'calendarIcon');
            $script = $this->getCalInitScript();
            $this->getForm()->setCalendarShown();
            return parent::toInput() . $img . $script;
        }
        
        if($this->showTime){
            $return = $this->getHourDropdown();
            if($this->showMinute){
                $return .= ":" . $this->getMinuteDropdown();
            }else{
                $return .= $this->getMinuteDropdown(true);
            }
            $return .= " " . $this->getPMDropdown();
            
            $return .= $this->getHiddenField();
        }else{
            $return = parent::toInput() . $this->getDateFormatLabel();
        }
        return $return;
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
    
    private function getHourDropdown(){
        $selectHour = new Dropdown("_" . $this->getName() . "_hour");
        $selectHour->set("id","_" . $this->getName() . "_hour");
        for($i=1;$i<13;$i++){
            $selectHour->add_option($i,$i);
        }
        $selectHour->set("class","dateBox");
        $selectHour->set("onchange","updateHiddenTimeField('".$this->getName()."')");
        return $selectHour;
    }
    
    private function getMinuteDropdown($hidden=false){
        if($hidden){
            $hidden = new Hidden("_" . $this->getName() . "_minute");
            $hidden->set("id","_" . $this->getName() . "_minute");
            $hidden->setValue("00");
            return $hidden;
        }
        $selectMinute = new Dropdown("_" . $this->getName() . "_minute");
        $selectMinute->set("class","dateBox");
        $selectMinute->set("id","_" . $this->getName() . "_minute");
        $selectMinute->set("onchange","updateHiddenTimeField('".$this->getName()."')");
        $selectMinute->add_option("00","00")->add_option("15","15")->add_option("30","30")->add_option("45","45");
        return $selectMinute;
    }
    
    private function getPMDropdown(){
        $selectPM = new Dropdown("_" . $this->getName() . "_pm");
        $selectPM->set("id","_" . $this->getName() . "_pm");
        $selectPM->add_option("am","am")->add_option("pm","pm");
        $selectPM->set("class","dateBox");
        $selectPM->set("onchange","updateHiddenTimeField('".$this->getName()."')");
        return $selectPM;
    }
    
    private function getHiddenField(){
        $hidden = new Hidden($this->getName());
        $hidden->setValue($this->getForm()->getValue($this->getName()));
        $hidden->set("id",$this->getName());
        $script = <<<EOP
<script type="text/javascript">
updateSelectTimeFields("{$this->getName()}");
</script>
EOP;
        return $hidden.$script;
    }

    private static function booleanToString($val) {
        return $val ? "true" : "false";
    }
}