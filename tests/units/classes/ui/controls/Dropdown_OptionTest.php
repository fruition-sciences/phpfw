<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Dropdown_Option.
 */
class Dropdown_OptionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Dropdown_Option
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \Dropdown_Option("france", "Montpellier");
    }

    /**
     * @covers Dropdown_Option::__construct
     */
    public function test__construct() {
        $this->assertSame("option", $this->object->getType());
    }

    /**
     * @covers Dropdown_Option::asString
     */
    public function testAsString() {
        $actual = $this->object->asString(array("Montpellier"));
        $excepted = '<option value="Montpellier" selected="selected">france</option>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Dropdown_Option::asString
     */
    public function testAsStringNotSelected() {
        $actual = $this->object->asString(array());
        $excepted = '<option value="Montpellier">france</option>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Dropdown_Option::ToString
     */
    public function testToString() {
        $actual = $this->object->toString();
        $excepted = 'france';
        $this->assertEquals($excepted, $actual);
    }
}
