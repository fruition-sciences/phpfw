<?php
/**
 * Date: 04/28/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for MenuItem.
 */
class MenuItemTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \MenuItem
     */
    protected $menuItem;

    /**
     * @SimpleXMLElement
     */
    protected $data;
    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
       $this->data = '<item id="dashboard" adminOnly="1">
                    <name>Dashboard</name>
                    <href>/vine/map</href>
                    <item id="Dashboard">
                        <name>MAP VIEW</name>
                        <href>/vine/map</href>
                    </item>
                </item>';

        $this->menuItem = new \MenuItem(new \Menu('tests/config/menu.xml'), new \SimpleXMLElement($this->data));
    }

    /**
     * @covers MenuItem::getId
     */
    public function testGetId() {
        $id = $this->menuItem->getId();
        $actual = "". $id;
        $excepted = "dashboard";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers MenuItem::getName
     */
    public function testGetName() {
        $name = $this->menuItem->getName();
        $actual = "". $name;
        $excepted = "Dashboard";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers MenuItem::getHref
     */
    public function testGetHref() {
        $href = $this->menuItem->getHref();
        $actual = "". $href;
        $excepted = "/vine/map";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers MenuItem::getItems
     * @covers MenuItem::__construct
     * @covers MenuItem::getXmlElementAttributes
     */
    public function testGetItems() {
        $actual = $this->menuItem->getItems();
        $this->assertContainsOnly('MenuItem', $actual);
    }

    /**
     * @covers MenuItem::isSelected
     */
    public function testIsSelected() {
        $this->menuItem->setSelected(true);
        $this->assertTrue($this->menuItem->isSelected());
    }

    /**
     * @covers MenuItem::setSelected
     */
    public function testSetSelected() {
        $this->assertFalse($this->menuItem->isSelected());
        $this->menuItem->setSelected(true);
        $this->assertTrue($this->menuItem->isSelected());
    }

    /**
     * @covers MenuItem::isAdminOnly
     */
    public function testIsAdminOnly() {
        $excepted = '1';
        $actual = $this->menuItem->isAdminOnly()->__toString();
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MenuItem::getXmlElement
     */
    public function testGetXmlElement() {
        $excepted = new \SimpleXMLElement($this->data);
        $actual = $this->menuItem->getXmlElement();
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MenuItem::getAttributes
     */
    public function testGetAttributes() {
        $excepted = array(
            'id' => 'dashboard',
            'adminOnly' => 1
        );
        $actual = $this->menuItem->getAttributes();
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers MenuItem::getAttribute
     */
    public function testGetAttribute() {
        $this->assertEquals('dashboard', $this->menuItem->getAttribute('id'));
    }

    /**
     * @covers MenuItem::getAttribute
     */
    public function testGetAttributeNull() {
        $this->assertNull($this->menuItem->getAttribute('notExist'));
    }
}
?>
