<?php
/**
 * Created on May 07 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for MeasureTextbox.
 */
class MeasureTextboxTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \MeasureTextbox
     */
    protected $measure;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->measure = new \MeasureTextbox("measure", "Zend_Measure_Temperature::CELSIUS");
    }

    /**
     * This method is called before the first test is executed.
     */
    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("Europe/Paris");
        $transaction->setUser($user);
    }

    /**
     * @covers MeasureTextbox::__construct
     * @covers MeasureTextbox::getDisplayUnit
     * @covers MeasureTextbox::setDisplayUnit
     */
    public function testSetDisplayUnit() {
        $measure = $this->measure->setDisplayUnit("Zend_Measure_Temperature::FAHRENHEIT");
        $actual = $measure->getDisplayUnit();
        $excepted = "Zend_Measure_Temperature::FAHRENHEIT";
        $this->assertEquals($excepted, $actual);

    }

    /**
     * @covers MeasureTextbox::toInput
     * @covers MeasureTextbox::getUnitSymbolIfShown
     * @covers MeasureTextbox::getUnitFieldName
     */
    public function testToInput() {
        $this->measure->setForm(new \Form());
        $actual = $this->measure->toInput();
        $excepted = '<input name="measure" type="text"></input><input name="measure__unit" type="hidden" value="Zend_Measure_Temperature::CELSIUS" id="measure__unit"></input> ';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MeasureTextbox::toInput
     * @covers MeasureTextbox::getUnitSymbolIfShown
     * @covers MeasureTextbox::setShowSymbol
     * @covers MeasureTextbox::getUnitSymbol
     */
    public function testToInputWithSymbol() {
        $this->measure->setForm(new \Form());
        $this->measure->setShowSymbol(true);
        $actual = $this->measure->toInput();
        $excepted = '<input name="measure" type="text"></input><input name="measure__unit" type="hidden" value="Zend_Measure_Temperature::CELSIUS" id="measure__unit"></input> °C';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MeasureTextbox::toString
     * @covers MeasureTextbox::convert
     */
    public function testToString() {
        $this->measure->setForm(new \Form());
        $this->measure->setValue("Temperature");
        $actual = $this->measure->toString();
        $excepted = "Temperature ";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MeasureTextbox::toString
     * @covers MeasureTextbox::convert
     */
    public function testToStringWithSymbol() {
        $this->measure->setForm(new \Form());
        $this->measure->setValue("25.4");
        $this->measure->setShowSymbol(true);
        $actual = $this->measure->toString();
        $excepted = "25.4 °C";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MeasureTextbox::setDecimalDigits
     * @covers MeasureTextbox::getDecimalDigits
     */
    public function testSetDecimalDigits() {
        $digit = 4;
        $actual = $this->measure->getDecimalDigits();
        $this->assertNull($actual);
        $measure = $this->measure->setDecimalDigits(4);
        $actual = $measure->getDecimalDigits();
        $this->assertEquals($digit, $actual);
    }
}
