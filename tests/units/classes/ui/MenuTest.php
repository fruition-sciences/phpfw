<?php
/**
 * Date: 04/28/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Menu.
 */
class MenuTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Menu
     */
    protected $menu;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->menu = new \Menu('tests/config/menu.xml');
    }

    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("America/Los_Angeles");
        $user->setLocale("en_US");
        $user->setIsAdmin(true);
        $transaction->setUser($user);
    }

    /**
     * @covers Menu::setMenuSelection
     */
    public function testSetMenuSelection() {
        $this->menu->setMenuSelection("others->sensors");
        $this->assertEquals("others", $this->menu->getMainMenuId());
        $this->assertEquals("sensors", $this->menu->getSubMenuId());
    }

    /**
     * @covers Menu::getItems
     */
    public function testGetItems() {
        $this->menu->setMenuSelection("dashboard->sensors");
        $actual = $this->menu->getItems();
        $this->assertContainsOnlyInstancesOf("MenuItem", $actual);
    }

    /**
     * @covers Menu::setMainMenuId
     * @covers Menu::__construct
     * @covers Menu::load
     */
    public function testSetMainMenuId() {
        $menuId = "Dashboard";
        $this->assertNull($this->menu->getMainMenuId());
        $this->menu->setMainMenuId($menuId);
        $this->assertEquals($menuId, $this->menu->getMainMenuId());
    }

    /**
     * @covers Menu::getMainMenuId
     */
    public function testGetMainMenuId() {
        $menuId = "Dashboard";
        $this->menu->setMainMenuId($menuId);
        $this->assertEquals($menuId, $this->menu->getMainMenuId());
    }

    /**
     * @covers Menu::setSubMenuId
     */
    public function testSetSubMenuId() {
        $menuId = "LIST VIEW";
        $this->assertNull($this->menu->getSubMenuId());
        $this->menu->setSubMenuId($menuId);
        $this->assertEquals($menuId, $this->menu->getSubMenuId());
    }

    /**
     * @covers Menu::getSubMenuId
     */
    public function testGetSubMenuId() {
        $menuId = "LIST VIEW";
        $this->menu->setSubMenuId($menuId);
        $this->assertEquals($menuId, $this->menu->getSubMenuId());
    }
}
?>
