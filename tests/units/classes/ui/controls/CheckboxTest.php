<?php
/**
 * Date: 05/05/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Checkbox.
 */
class CheckboxTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Checkbox
     */
    protected $checkbox;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->checkbox = new \Checkbox("DateChoice");
    }

    /**
     * @covers Checkbox::__construct
     */
    public function test__construct() {
        $this->checkbox = new \Checkbox("DateChoice");
        $actual = $this->checkbox->get("type");
        $excepted = "checkbox";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Checkbox::setVal
     */
    public function testSetVal() {
        $this->checkbox->setVal(14);
        $actual = $this->checkbox->get("value");
        $excepted = 14;
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Checkbox::setValue
     */
    public function testSetValue() {
        $this->checkbox = new \Checkbox("DateChoice");
        $this->checkbox->setVal(14);
        $this->checkbox = $this->checkbox->setValue(14);
        $actual = $this->checkbox->toString();
        $this->assertContains("checked", $actual);
    }

    /**
     * @covers Checkbox::toInput
     */
    public function testToInput() {
        $actual = $this->checkbox->toInput();
        $excepted = '<input name="DateChoice" type="checkbox" value="1"></input>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Checkbox::toInput
     */
    public function testToInputChecked() {
        $this->checkbox = $this->checkbox->setValue(14);
        $this->checkbox->setVal(14);
        $actual = $this->checkbox->toInput();
        $excepted = '<input name="DateChoice" type="checkbox" value="14" checked></input>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Checkbox::toInput
     */
    public function testToInputCheckedMulti() {
        $this->checkbox = $this->checkbox->setValue(array(14, 21));
        $this->checkbox->setVal(14);
        $actual = $this->checkbox->toInput();
        $excepted = '<input name="DateChoice" type="checkbox" value="14" checked></input>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Checkbox::toString
     */
    public function testToString() {
        $actual = $this->checkbox->toString();
        $excepted = '<input name="DateChoice" type="checkbox" value="1" disabled></input>';
        $this->assertEquals($excepted, $actual);
    }
}
