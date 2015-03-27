<?php
/**
 * Date: 05/05/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Dropdown.
 */
class DropdownTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Dropdown
     */
    protected $drop;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->drop = new \Dropdown("Office Name");
    }

    /**
     * @covers Dropdown::__construct
     */
    public function test__construct() {
        $this->drop = new \Dropdown("Office Name");
        $this->assertSame("select", $this->drop->getType());
    }

    /**
     * @covers Dropdown::add_option
     */
    public function testAdd_option() {
        $name = "napa";
        $value = "valley";
        $dropDown = $this->drop->add_option($name, $value);
        $actual = $dropDown->getOptions();
        $excepted = new \Dropdown_Option($name, $value);
        $this->assertContainsOnlyInstancesOf("Dropdown_Option", $actual);
        $this->assertEquals($excepted, $actual[0]);
    }

    /**
     * @covers Dropdown::addOption
     */
    public function testAddOption() {
        $name = "napa";
        $value = "valley";
        $dropDown = $this->drop->addOption($name, $value);
        $actual = $dropDown->getOptions();
        $excepted = new \Dropdown_Option($name, $value);
        $this->assertContainsOnlyInstancesOf("Dropdown_Option", $actual);
        $this->assertEquals($excepted, $actual[0]);
    }

    /**
     * @covers Dropdown::addOption
     */
    public function testAddOptionTooltip() {
        $name = "napa";
        $value = "valley";
        $tooltip = "choose office";
        $dropDown = $this->drop->addOption($name, $value, $tooltip);
        $actual = $dropDown->getOptions();
        $excepted = new \Dropdown_Option($name, $value);
        $excepted->setTooltip($tooltip);
        $this->assertContainsOnlyInstancesOf("Dropdown_Option", $actual);
        $this->assertEquals($excepted, $actual[0]);
    }

    /**
     * @covers Dropdown::getOptions
     */
    public function testGetOptions() {
        $name = "napa";
        $value = "valley";
        $dropDown = $this->drop->addOption($name, $value);
        $actual = $dropDown->getOptions();
        $excepted = new \Dropdown_Option($name, $value);
        $this->assertEquals($excepted, $actual[0]);
    }

    /**
     * @covers Dropdown::addOptgroup
     */
    public function testAddOptgroup() {
        $name = "offices";
        $dropDown = $this->drop->addOptgroup($name);
        $actual = $dropDown->getOptgroups();
        $excepted = new \Dropdown_Optgroup($name);
        $this->assertContainsOnlyInstancesOf("Dropdown_Optgroup", $actual);
        $this->assertEquals($excepted, $actual[0]);
    }

    /**
     * @covers Dropdown::getOptgroups
     */
    public function testGetOptgroups() {
        $name = "offices";
        $dropDown = $this->drop->addOptgroup($name);
        $actual = $dropDown->getOptgroups();
        $excepted = new \Dropdown_Optgroup($name);
        $this->assertEquals($excepted, $actual[0]);
    }

    /**
     * @covers Dropdown::addOptgroupObject
     */
    public function testAddOptgroupObject() {
        $name = "offices";
        $dropDown = $this->drop->addOptgroup(new \Dropdown_Optgroup($name));
        $actual = $dropDown->getOptgroups();
        $excepted = new \Dropdown_Optgroup($name);
        $this->assertContainsOnlyInstancesOf("Dropdown_Optgroup", $actual);
    }

    /**
     * @covers Dropdown::setReadonlyLink
     */
    public function testSetReadonlyLink() {
        $this->assertNull($this->drop->getReadonlyLink());
        $link = new \Link("fruitionsciences.com", "Fruition sciences");
        $this->drop->setReadonlyLink($link);
        $actual = $this->drop->getReadonlyLink();
        $this->assertEquals($link, $actual);
    }

    /**
     * @covers Dropdown::getReadonlyLink
     */
    public function testGetReadonlyLink() {
        $link = new \Link("fruitionsciences.com", "Fruition sciences");
        $this->drop->setReadonlyLink($link);
        $actual = $this->drop->getReadonlyLink();
        $this->assertEquals($link, $actual);
    }

    /**
     * @covers Dropdown::__toString
     * @covers Dropdown::options_as_string
     */
    public function test__toString() {
        $optGp = new \Dropdown_Optgroup("dates");
        $this->drop = $this->drop->addOption("office", "fruition sciences");
        $this->drop = $this->drop->addOptgroupObject($optGp);
        $actual = $this->drop->__toString();
        $excepted = '<select name="Office Name"><option value="fruition sciences">office</option><optgroup label="dates"></optgroup></select>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Dropdown::toString
     * @todo We should add options value in Dropdown::values when adding them.
     */
    public function testToString() {
        $optionValue = array("Fruition Napa", "Fruition Paris", "Fruition Brazil");
        $this->drop->setValue($optionValue);
        $opt = new \Dropdown_Optgroup("Offices");
        $opt = $opt->addOption("napa", "Fruition Napa");
        $opt = $opt->addOption("paris", "Fruition Paris");
        $this->drop->addOptgroupObject($opt);
        $this->drop = $this->drop->addOption("brazilia", "Fruition Brazil");
        $actual = $this->drop->toString();
        $excepted = '<option value="Fruition Brazil">brazilia</option>, <option value="Fruition Napa">napa</option>, <option value="Fruition Paris">paris</option>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Dropdown::toString
     */
    public function testToStringWithLink() {
        $optionValue = array("Fruition Brazil");
        $this->drop->setValue($optionValue);
        $this->drop = $this->drop->addOption("brazilia", "Fruition Brazil");
        $this->drop->setReadonlyLink(new \Link("fruitionsciences.com", "Fruition Sciences"));
        $actual = $this->drop->toString();
        $excepted = '<a href="fruitionsciences.com"><option value="Fruition Brazil">brazilia</option></a>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Dropdown::setValue
     */
    public function testSetValueArray() {
        $data = array("Californie", "Paris");
        $this->assertEmpty($this->drop->getValues());
        $this->drop = $this->drop->setValue($data);
        $actual = $this->drop->getValues();
        $this->assertEquals($data, $actual);
    }

    /**
     * @covers Dropdown::setValue
     */
    public function testSetValue() {
        $city = "Montpellier";
        $this->assertEmpty($this->drop->getValues());
        $this->drop = $this->drop->setValue($city);
        $actual = $this->drop->getValues();
        $excepted = array($city);
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Dropdown::getValues
     */
    public function testGetValue() {
        $data = array("Californie", "Paris");
        $this->drop = $this->drop->setValue($data);
        $actual = $this->drop->getValues();
        $this->assertEquals($data, $actual);
    }

    /**
     * @covers Dropdown::setMultiSelectReadonlySeparator
     */
    public function testSetMultiSelectReadonlySeparator() {
        $this->drop->setMultiSelectReadonlySeparator(':');
        $this->assertEquals(":", $this->drop->getMultiSelectReadonlySeparator());
    }

    /**
     * @covers Dropdown::GetMultiSelectReadonlySeparator
     */
    public function testGetMultiSelectReadonlySeparator() {
        $this->assertEquals(", ", $this->drop->getMultiSelectReadonlySeparator());
    }
}
