<?php
/**
 * Date: 05/05/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Dropdown_Optgroup.
 */
class Dropdown_OptgroupTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Dropdown_Optgroup
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \Dropdown_Optgroup("Offices");
    }

    /**
     * @covers Dropdown_Optgroup::__construct
     */
    public function test__construct() {
        $this->assertSame("optgroup", $this->object->getType());
    }

    /**
     * @covers Dropdown_Optgroup::addOption
     */
    public function testAddOption() {
        $name = "napa";
        $value = "valley";
        $this->assertEmpty($this->object->getOptions());
        $dropDown = $this->object->addOption($name, $value);
        $actual = $dropDown->getOptions();
        $expected = new \Dropdown_Option($name, $value);
        $this->assertContainsOnlyInstancesOf("Dropdown_Option", $actual);
        $this->assertEquals($expected, $actual[0]);
    }

    /**
     * @covers Dropdown_Optgroup::asString
     * @covers Dropdown_Optgroup::options_as_string
     */
    public function testAsString() {
        $dropDown = $this->object->addOption("napa", "valley");
        $dropDown = $dropDown->addOption("montpellier", "Fruition");
        $actual = $dropDown->asString(array("valley"));
        $expected = '<optgroup label="Offices"><option value="valley" selected="selected">napa</option><option value="Fruition">montpellier</option></optgroup>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Dropdown_Optgroup::asString
     */
    public function testAsStringMutltiple() {
        $dropDown = $this->object->addOption("napa", "valley");
        $dropDown = $dropDown->addOption("montpellier", "Fruition");
        $actual = $dropDown->asString(array("valley", "Fruition"));
        $expected = '<optgroup label="Offices"><option value="valley" selected="selected">napa</option><option value="Fruition" selected="selected">montpellier</option></optgroup>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Dropdown_Optgroup::toString
     */
    public function testToString() {
        $dropDown = $this->object->addOption("napa", "valley");
        $dropDown = $dropDown->addOption("montpellier", "Fruition");
        $actual = $dropDown->toString();
        $expected = 'Offices';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Dropdown_Optgroup::getOptions
     */
    public function testgetOptions() {
        $name = "napa";
        $value = "valley";
        $dropDown = $this->object->addOption($name, $value);
        $actual = $dropDown->getOptions();
        $expected = new \Dropdown_Option($name, $value);
        $this->assertContainsOnlyInstancesOf("Dropdown_Option", $actual);
        $this->assertEquals($expected, $actual[0]);
    }
}
