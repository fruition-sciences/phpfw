<?php
/**
 * Date: 05/05/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Datebox.
 */
class DateboxTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Datebox
     */
    protected $control;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->control = new \Datebox("select");
    }

    /**
     * This method is called before the first test is executed.
     */
    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("Europe/Paris");
        $transaction->setUser($user);

        $zend_locale = new \Zend_Locale("en_US");
        \Zend_Registry::set('Zend_Locale', $zend_locale);
    }

    /**
     * @covers Datebox::__construct
     */
    public function test__construct() {
        print_r($this->control->toString());
        $this->assertEquals("input", $this->control->getType());
        $this->assertContains("date", $this->control->getElementOpenTag());
    }

    /**
     * @covers Datebox::showTime
     */
    public function testShowTime() {
        $this->assertFalse($this->control->isShowTime());
        $this->control = $this->control->showTime();
        $this->assertTrue($this->control->isShowTime());
    }

    /**
     * @covers Datebox::showTimeOnly
     * @covers Datebox::isShowTime
     * @covers Datebox::isShowDate
     * @covers Datebox::isShowPopup
     */
    public function testShowTimeOnly() {
        $control = $this->control->showTimeOnly();
        $this->assertTrue($control->isShowTime());
        $this->assertFalse($control->isShowDate());
        $this->assertFalse($control->isShowMinute());
        $this->assertFalse($control->isShowPopup());
        $this->assertContains("time", $control->getElementOpenTag());
    }

    /**
     * @covers Datebox::showMinute
     * @covers Datebox::isShowMinute
     */
    public function testShowMinute() {
        $control = $this->control->showMinute();
        $this->assertTrue($control->isShowMinute());
        $control = $this->control->showMinute();
        $this->assertTrue($control->isShowMinute());
    }

    /**
     * @covers Datebox::noPopup
     */
    public function testNoPopup() {
        $this->assertTrue($this->control->isShowPopup());
        $this->control->noPopup();
        $this->assertFalse($this->control->isShowPopup());
    }

    /**
     * @covers Datebox::setYearRange
     * @covers Datebox::getYearRange
     */
    public function testSetYearRange() {
        $control = $this->control->setYearRange("2002:2012");
        $actual = $control->getYearRange();
        $expected = "2002:2012";
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers Datebox::setStepMinute
     * @covers Datebox::getStepMinute
     */
    public function testSetStepMinute() {
        $step = 18;
        $control = $this->control->setStepMinute($step);
        $actual = $control->getStepMinute();
        $this->assertSame($actual, $step);
    }

    /**
     * @covers Datebox::toString
     */
    public function testToString() {
        $this->control->setValue("13 june");
        $actual =  $this->control->toString();
        $expected = "13 june";
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers Datebox::toInput
     * @covers Datebox::getCalInitScript
     * @covers Datebox::getDatePickerLocalization
     */
    public function testToInputPopup() {
        $this->control->setForm(new \Form());
        $this->control->setValue("13 june");
        $this->control->showTime();
        $this->control->showMinute();
        $actual =  $this->control->toInput();
        $expected = '<input name="select" type="text" id="select" value="13 june" class="dateTime"></input><script type="text/javascript">';
        $this->assertContains($expected, $actual);
    }

    /**
     * @covers Datebox::toInput
     * @covers Datebox::getCalInitScript
     * @covers Datebox::getDatePickerLocalization
     */
    public function testToInputPopupNoTime() {
        $this->control->setForm(new \Form());
        $this->control->setValue("13 june");
        $actual =  $this->control->toInput();
        $expected = '<input name="select" type="text" id="select" value="13 june" class="date"></input><script type="text/javascript">';
        $this->assertContains($expected, $actual);
    }

    /**
     * @covers Datebox::toInput
     * @covers Datebox::getCalInitScript
     * @covers Datebox::getDateFormatLabel
     */
    public function testToInput() {
        $this->control->setForm(new \Form());
        $this->control->setValue("13 june");
        $this->control->noPopup();
        $actual =  $this->control->toInput();
        $expected = '<input name="select" type="text" id="select" value="13 june" class="date"></input>MM/DD/YY';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Datebox::toInput
     * @covers Datebox::getCalInitScript
     * @covers Datebox::getMinuteDropdown
     */
    public function testToInputShowTime() {
        $this->control->setForm(new \Form());
        $this->control->setValue("13 june");
        $this->control->noPopup();
        $this->control->showTime();
        $actual =  $this->control->toInput();
        $expected = '<select name="_select_hour" id="_select_hour" onchange="updateHiddenTimeField(\'select\')" class="dateBox">';
        $this->assertContains($expected, $actual);
    }

    /**
     * @covers Datebox::toInput
     * @covers Datebox::getCalInitScript
     * @covers Datebox::getHourDropdown
     * @covers Datebox::getMinuteDropdown
     * @covers Datebox::getPMDropdown
     * @covers Datebox::getHiddenField
     */
    public function testToInputShowTimeMinute() {
        $this->control->setForm(new \Form());
        $this->control->setValue("13 june");
        $this->control->noPopup();
        $this->control->showTime();
        $this->control->showMinute();
        $actual =  $this->control->toInput();
        $expected = '<select name="_select_minute" id="_select_minute" onchange="updateHiddenTimeField(\'select\')" class="dateBox">';
        $this->assertContains($expected, $actual);
    }
}
