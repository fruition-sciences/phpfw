<?php
/**
 * Created on May 18 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for User.
 */
class UserTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \User
     */
    protected $user;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->user = new \User();
    }

    /**
     * @covers User::setId
     * @covers User::getId
     */
    public function testSetId() {
        $id = 3;
        $actual = $this->user->getId();
        $this->assertNotEquals($id, $actual);
        $this->user->setId($id);
        $actual = $this->user->getId();
        $this->assertEquals($id, $actual);
    }

    /**
     * @covers User::setAlias
     * @covers User::getAlias
     */
    public function testSetAlias() {
        $alias = "alias";
        $actual = $this->user->getAlias();
        $this->assertNotEquals($alias, $actual);
        $this->user->setAlias($alias);
        $actual = $this->user->getAlias();
        $this->assertEquals($alias, $actual);
    }

    /**
     * @covers User::getName
     * @covers User::SetName
     */
    public function testSetName() {
        $name = "McGill";
        $actual = $this->user->getName();
        $this->assertNotEquals($name, $actual);
        $this->user->setName($name);
        $actual = $this->user->getName();
        $this->assertEquals($name, $actual);
    }

    /**
     * @covers User::setIsAdmin
     * @covers User::isAdmin
     */
    public function testSetIsAdmin() {
        $actual = $this->user->isAdmin();
        $this->assertNull($actual);
        $this->user->setIsAdmin(true);
        $actual = $this->user->isAdmin();
        $this->assertTrue($actual);
    }

    /**
     * @covers User::setGroupId
     * @covers User::getGroupId
     */
    public function testSetGroupId() {
        $id = 13;
        $actual = $this->user->getGroupId();
        $this->assertNotEquals($id, $actual);
        $this->user->setGroupId($id);
        $actual = $this->user->getGroupId();
        $this->assertEquals($id, $actual);
    }

    /**
     * @covers User::setTimezone
     * @covers User::getTimezone
     */
    public function testSetTimezone() {
        $timezone = 'America/Los_Angeles';
        $actual = $this->user->getTimezone();
        $this->assertNotEquals($timezone, $actual);
        $this->user->setTimezone($timezone);
        $actual = $this->user->getTimezone();
        $this->assertEquals($timezone, $actual);
    }

    /**
     * @covers User::setLocale
     * @covers User::getLocale
     */
    public function testSetLocale() {
        $local = 'fr_Fr';
        $actual = $this->user->getLocale();
        $this->assertNotEquals($local, $actual);
        $this->user->setLocale($local);
        $actual = $this->user->getLocale();
        $this->assertEquals($local, $actual);
    }

    /**
     * @covers User::isAnonymous
     * @covers User::setId
     */
    public function testIsAnonymous() {
        $this->user->setId(0);
        $actual = $this->user->isAnonymous();
        $this->assertTrue($actual);
    }

}
