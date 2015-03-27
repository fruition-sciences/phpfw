<?php
/**
 * Created on May 13 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Control.
 */
class ControlTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Control
     */
    protected $control;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->control = new \Control("DateChoice");
    }

    /**
     * @covers Control::setForm
     */
    public function testSetForm() {
        $form = new \Form();
        $actual = $this->control->getForm();
        $this->assertNull($actual);
        $this->control->setForm($form);
        $actual = $this->control->getForm();
        $this->assertEquals($form, $actual);
    }

    /**
     * @covers Control::getForm
     */
    public function testGetForm() {
        $form = new \Form();
        $this->control->setForm($form);
        $actual = $this->control->getForm();
        $this->assertEquals($form, $actual);
    }

    /**
     * @covers Control::setReadonly
     */
    public function testSetReadonly() {
        $actual = $this->control->isReadonly();
        $this->assertNull($actual);
        $this->control->setReadonly(true);
        $actual = $this->control->isReadonly();
        $this->assertTrue($actual);
    }

    /**
     * @covers Control::isReadonly
     */
    public function testIsReadonly() {
        $this->control->setReadonly(false);
        $actual = $this->control->isReadonly();
        $this->assertFalse($actual);
    }

    /**
     * @covers Control::__toString
     */
    public function test__toString() {
        $actual = $this->control->__toString();
        $excepted = "<DateChoice ></DateChoice>";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Control::__toString
     */
    public function test__toStringReadOnly() {
        $this->control->setReadonly(true);
        $this->control->setValue("Wednesday");
        $actual = $this->control->__toString();
        $excepted = "Wednesday";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Control::toString
     */
    public function testToString() {
        $this->control->setValue("Wednesday");
        $actual = $this->control->toString();
        $excepted = "Wednesday";
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Control::toString
     */
    public function testToStringEmpty() {
        $actual = $this->control->toString();
        $this->assertEmpty($actual);
    }

    /**
     * @covers Control::toInput
     */
    public function testToInput() {
        $actual = $this->control->toInput();
        $excepted = '<DateChoice ></DateChoice>';
        $this->assertEquals($excepted, $actual);
    }
}
