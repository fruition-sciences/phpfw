<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Radio.
 */
class RadioTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Radio
     */
    protected $radio;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->radio = new \Radio("dates", "DatesChoices");
    }

    /**
     * @covers Radio::__construct
     */
    public function test__construct() {
        $this->assertEquals("input", $this->radio->getType());
        $this->assertEquals("radio", $this->radio->get("type"));
    }

    /**
     * @covers Radio::setVal
     */
    public function testSetVal() {
        $value = "June";
        $radio = $this->radio->setVal($value);
        $actual = $radio->getValue();
        $this->assertEquals($value, $actual);
    }

    /**
     * @covers Radio::setValue
     */
    public function testSetValue() {
        $value = "June";
        $radio = $this->radio->setValue($value);
        $this->assertInstanceOf("Radio", $radio);
    }

    /**
     * @covers Radio::toInput
     */
    public function testToInput() {
        $actual = $this->radio->toInput();
        $expected = '<input name="dates" type="radio" value="DatesChoices"></input>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Radio::toInput
     */
    public function testToInputChecked() {
        $value = "June";
        $radio = $this->radio->setVal($value);
        $radio = $radio->setValue($value);
        $actual = $radio->toInput();
        $expected = '<input name="dates" type="radio" value="June" checked></input>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Radio::toString
     */
    public function testToString() {
        $actual = $this->radio->toString();
        $expected = '<input name="dates" type="radio" value="DatesChoices" disabled></input>';
        $this->assertEquals($expected, $actual);
    }

}
