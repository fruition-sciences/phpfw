<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Datebox extends Control {
    /**
     * The range of years displayed in the year drop-down in the datePicker
     * @var String
     * @see http://api.jqueryui.com/datepicker/#option-yearRange
     */
    private $yearRange = "1980:+0";
    
    private static $dateFormatLabel = "MM/DD/YY";
    private static $timeFormatLabel = "HH:MM pm";
    private static $dateTimeFormatLabel = "MM/DD/YY HH:MM pm";

    private $showTime = false;
    private $showDate = true;
    private $showPopup = true;
    private $showMinute = false;
    /**
     * Minutes per step the slider makes.
     * @var int
     */
    private $stepMinute = 5;

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
    
    public function noPopup() {
        $this->showPopup = false;
    }
    
    public function setYearRange($yearRange) {
        $this->yearRange = $yearRange;
        return $this;
    }
    
    public function setStepMinute($stepMinute) {
        $this->stepMinute = $stepMinute;
        return $this;
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
        if ($this->showPopup){
            $script = $this->getCalInitScript();
            $this->getForm()->setCalendarShown();
            return parent::toInput() . $script;
        }
        
        if ($this->showTime){
            $return = $this->getHourDropdown();
            if($this->showMinute){
                $return .= ":" . $this->getMinuteDropdown();
            }else{
                $return .= $this->getMinuteDropdown(true);
            }
            $return .= " " . $this->getPMDropdown();
            
            $return .= $this->getHiddenField();
        } else{
            $return = parent::toInput() . $this->getDateFormatLabel();
        }
        return $return;
    }

    private function getCalInitScript() {
        $localization = json_encode($this->getDatePickerLocalization());
        if ($this->showTime) {
            $showMinute = $this->showMinute ? "true" : "false";
            $script = <<<EOP
<script type="text/javascript">
    jQuery(function() {
        jQuery.timepicker.setDefaults($localization);
        jQuery( "#{$this->getName()}" ).datetimepicker({
            changeYear: true,
            yearRange: "{$this->yearRange}",
            showOtherMonths: true,
            selectOtherMonths: true,
            stepMinute: {$this->stepMinute},
            showMinute: {$showMinute},
            firstDay: 1
        });
    });
</script>
EOP;
        } else {
            $script = <<<EOP
<script type="text/javascript">
    jQuery(function() {
        jQuery.datepicker.setDefaults($localization);
        jQuery( "#{$this->getName()}" ).datepicker({
            changeYear: true,
            yearRange: "{$this->yearRange}",
            showOtherMonths: true,
            selectOtherMonths: true,
            firstDay: 1
        });
    });
</script>
EOP;
        }
        
        return $script;
    }

    private function getButtonName() {
        return $this->getName() . "_button";
    }
    
    private function getHourDropdown(){
        $selectHour = new Dropdown("_" . $this->getName() . "_hour");
        $selectHour->set("id","_" . $this->getName() . "_hour");
        for($i=1;$i<13;$i++){
            $selectHour->addOption($i,$i);
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
        $selectMinute->addOption("00","00")->addOption("15","15")->addOption("30","30")->addOption("45","45");
        return $selectMinute;
    }
    
    private function getPMDropdown(){
        $selectPM = new Dropdown("_" . $this->getName() . "_pm");
        $selectPM->set("id","_" . $this->getName() . "_pm");
        $selectPM->addOption("am","am")->addOption("pm","pm");
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
    
    /**
     * Localization array for the Jquery UI date time picker.
     * @return array
     */
    private function getDatePickerLocalization() {        
        $daysArr = Zend_Locale_data::getList(Transaction::getInstance()->getUser()->getLocale(), "days");
        $weekArr = Zend_Locale_data::getList(Transaction::getInstance()->getUser()->getLocale(), "week");
        $firstDay = $daysArr['format']['narrow'][$weekArr["firstDay"]]-1;
        $tr = Application::getTranslator();
        // Most i18n values come from dedicated jquery.ui.datepicker-<LANG>.js
        // Make sure to include this file on all pages.
        // The fields defined here are the ones used by the datetimepicker extension.
        $regional = array(
            "closeText"          => $tr->_('Done'), // Display text for close link
            // Important: dateFormat must be compatible with pattern used by Formatter::date for each locale
            "dateFormat"         => $tr->_('m/dd/yy'), // Single y means 2 digit year.
            "firstDay"           => $firstDay, // The first day of the week, Sun = 0, Mon = 1, ...
            "isRTL"              => false, // True if right-to-left language, false if left-to-right
            "showMonthAfterYear" => false, // True if the year select precedes month, false for month then year
            "yearSuffix"         => '' // Additional text to append to the year in the month headers
        );
        if ($this->showTime) {
            $regional["currentText"]   = $tr->_('Now');
            $regional["amNames"]       = array('AM', 'A');
            $regional["pmNames"]       = array('PM', 'P');
            // Important: timeFormat must be compatible with pattern used by Formatter::datetime for each locale
            $regional["timeFormat"]    = $tr->_('h:mm TT');
            $regional["timeSuffix"]    = '';
            $regional["timeOnlyTitle"] = $tr->_('Choose Time');
            $regional["timeText"]      = $tr->_('Time');
            $regional["hourText"]      = $tr->_('Hour');
            $regional["minuteText"]    = $tr->_('Minute');
            $regional["secondText"]    = $tr->_('Second');
            $regional["millisecText"]  = $tr->_('Millisecond');
            $regional["timezoneText"]  = $tr->_('Time Zone');
        }
        return $regional;
    }
}