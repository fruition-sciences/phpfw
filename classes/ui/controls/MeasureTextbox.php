<?php
/*
 * A textbox for values with a measure unit.
 * Works with a hidden field which holds the unit.
 * 
 * Created on Jan 28, 2011
 * Author: Yoni Rosenbaum
 *
 */

class MeasureTextbox extends Textbox {
    private $showSymbol; // boolean
    private $displayUnit; // String. Full Zend_Measure unit. For example: 'Zend_Measure_Temperature::CELSIUS'
    private $measureValue;
    private $decimalDigits = 2; // Number of decimal digits to show

    /** 
     * @param $name the name for this control.
     * @param $displayUnit String Full Zend_Measure unit.
     *        For example: 'Zend_Measure_Temperature::CELSIUS'
     * @return unknown_type
     */
    public function __construct($name, $displayUnit) {
        parent::__construct($name);
        $this->displayUnit = $displayUnit;
    }

    /**
     * Define the measure unit for this control. This will be overrridden by
     * the unit as defined in the form.
     * 
     * @param $displayUnit String Full Zend_Measure unit.
     *        For example: 'Zend_Measure_Temperature::CELSIUS'
     * @return MeasureTextbox
     */
    public function setDisplayUnit($displayUnit) {
        $this->displayUnit = $displayUnit;
        return $this;
    }

    public function getDisplayUnit() {
        return $this->displayUnit;
    }

    /**
     * If set to true, the unit symbol will show along with the control.
     * 
     * @param $showUnitSymbol
     * @return MeasureTextbox
     */
    public function setShowSymbol($showSymbol) {
        $this->showSymbol = $showSymbol;
        return $this;
    }

    public function toInput() {
        $hidden = $this->getForm()->hidden($this->getUnitFieldName());
        $hidden->setValue($this->displayUnit)->set("id", $this->getUnitFieldName());
        $this->convert();
        return parent::toInput() . $hidden . ' ' . $this->getUnitSymbolIfShown();
    }

    public function toString() {
        $this->convert();
        return parent::toString() . ' ' . $this->getUnitSymbolIfShown();
    }

    /**
     * Get the unit from the form. If it's defined and different than the
     * display unit, convert the value and set it back into this control's value.
     * Then, format with proper number of digits.
     * 
     * Do not call this method more than once!
     */
    private function convert() {
        if($this->getValue() !== null){
            $form = $this->getForm();
            // Get the 'hidden' value, which indicates the unit of the current value.
            $unit = $form->getValue($this->getUnitFieldName());
            // If the unit is different, convert
            if ($unit && $unit != $this->displayUnit) {
                $measure = MeasureUtils::newMeasure($unit, $this->getValue());
                $inutInfo = MeasureUtils::getUnitInfo($this->displayUnit);
                $measure->setType($inutInfo['constantName']);
                // Sets the new value
                $this->setValue($measure->getValue());
            }
            // TODO: Use formatter to be local aware
            $this->setValue(number_format($this->getValue(), $this->decimalDigits));
        }
    }

    private function getUnitFieldName() {
        return $this->getName() . '__unit';
    }

    private function getUnitSymbolIfShown() {
        if (!$this->showSymbol) {
            return '';
        }
        return $this->getUnitSymbol();
    }

    /**
     * @return String
     */
    private function getUnitSymbol() {
        $unit = $this->displayUnit;

        // Get symbol from unit name
        $zendMeasureObj = MeasureUtils::newMeasure($unit);
        $convList = $zendMeasureObj->getConversionList();
        return $convList[$zendMeasureObj->getType()][1];
    }

    /**
     * Set the number of decimal digits to show.
     * 
     * @param $decimalDigits
     * @return MeasureTextbox
     */
    public function setDecimalDigits($decimalDigits) {
        $this->decimalDigits = $decimalDigits;
        return $this;
    }

    public function getDecimalDigits() {
        return $this->decimalDigits;
    }
}