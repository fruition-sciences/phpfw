<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Datebox extends Control {
    private $dateFormat = "m/d/y";
    private $timeFormat = "hh:mm tt";
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
        jQuery( "#{$this->getName()}" ).datetimepicker({
            changeYear: true,
            yearRange: "{$this->yearRange}",
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "{$this->dateFormat}",
            timeFormat: "{$this->timeFormat}",
            stepMinute: {$this->stepMinute},
            showMinute: {$showMinute}
        });
        jQuery.datepicker.setDefaults($localization);
        jQuery( "#{$this->getName()}" ).datetimepicker();
    });
</script>
EOP;
        } else {
            $script = <<<EOP
<script type="text/javascript">
    jQuery(function() {
        jQuery( "#{$this->getName()}" ).datepicker({
            changeYear: true,
            yearRange: "{$this->yearRange}",
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "{$this->dateFormat}"
        });
        jQuery.datepicker.setDefaults($localization);
        jQuery( "#{$this->getName()}" ).datepicker();
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
        $regional = array(
            "closeText"          => $tr->_('Done'), // Display text for close link
            "prevText"           => $tr->_('Prev'), // Display text for previous month link
            "nextText"           => $tr->_('Next'), // Display text for next month link
            "currentText"        => $tr->_('Today'), // Display text for current month link
            "monthNames"         => array($tr->_('January'),$tr->_('February'),$tr->_('March'),$tr->_('April'),$tr->_('May'),$tr->_('June'),
                    			          $tr->_('July'),$tr->_('August'),$tr->_('September'),$tr->_('October'),
                    			          $tr->_('November'),$tr->_('December')), // Names of months for drop-down and formatting
            "monthNamesShort"    => array($tr->_('Jan'), $tr->_('Feb'), $tr->_('Mar'), $tr->_('Apr'), $tr->_('May'), $tr->_('Jun'), 
                                          $tr->_('Jul'), $tr->_('Aug'), $tr->_('Sep'), $tr->_('Oct'), $tr->_('Nov'), $tr->_('Dec')), // For formatting
            "dayNames"           => array($tr->_('Sunday'), $tr->_('Monday'), $tr->_('Tuesday'), $tr->_('Wednesday'), 
                                          $tr->_('Thursday'), $tr->_('Friday'), $tr->_('Saturday')), // For formatting
            "dayNamesShort"      => array($tr->_('Sun'), $tr->_('Mon'), $tr->_('Tue'), $tr->_('Wed'), $tr->_('Thu'), $tr->_('Fri'), $tr->_('Sat')), // For formatting
            "dayNamesMin"        => array($tr->_('Su'),$tr->_('Mo'),$tr->_('Tu'),$tr->_('We'),$tr->_('Th'),$tr->_('Fr'),$tr->_('Sa')), // Column headings for days starting at Sunday
            "weekHeader"         => $tr->_('Wk'), // Column header for week of the year
            "dateFormat"         => 'mm/dd/yy', // TODO See format options on parseDate
            "firstDay"           => $firstDay, // The first day of the week, Sun = 0, Mon = 1, ...
            "isRTL"              => false, // True if right-to-left language, false if left-to-right
            "showMonthAfterYear" => false, // True if the year select precedes month, false for month then year
            "yearSuffix"         => '' // Additional text to append to the year in the month headers
        );
        if($this->showTime) {
            $regional["currentText"]   = $tr->_('Now');
            $regional["amNames"]       = array('AM', 'A');
            $regional["pmNames"]       = array('PM', 'P');
            $regional["timeFormat"]    = 'HH:mm';
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