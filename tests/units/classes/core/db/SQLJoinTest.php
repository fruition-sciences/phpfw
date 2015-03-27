<?php
/**
 * created on Apr 22 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;

/**
 * Test class for SQLJoin.
 */
class SQLJoinTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \SQLJoin
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \SQLJoin("users", "us", "u.id = acc.id", \SQLJoin::LEFT_JOIN);
    }

    /**
    * @covers SQLJoin::__construct
    */
    public function test__construct() {
        $this->assertEquals("users", $this->object->getTable());
        $this->assertEquals("us", $this->object->getAlias());
        $this->assertEquals("u.id = acc.id", $this->object->getCondition());
        //$this->assertEquals("users", $this->object->getTable());
    }
    /**
     * @covers SQLJoin::getTable
     */
    public function testGetTable() {
        $this->assertSame('users', $this->object->getTable());
    }

    /**
     * @covers SQLJoin::getAlias
     */
    public function testGetAlias() {
        $this->assertSame('us', $this->object->getAlias());
    }

    /**
     * @covers SQLJoin::getCondition
     */
    public function testGetCondition() {
        $this->assertSame("u.id = acc.id", $this->object->getCondition());
    }

    /**
     * @covers SQLJoin::__toString
     */
    public function test__toString() {
        $this->assertSame("left join users us on (u.id = acc.id)", $this->object->__toString());
    }
}
?>
