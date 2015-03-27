<?php
/**
 * Date: 28/04/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for UserError.
 */
class UserErrorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var UserError
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \UserError;
    }

    /**
     * @covers UserError::setFieldName
     */
    public function testSetFieldName() {
        $field = "company";
        $this->assertNull($this->object->getFieldName());
        $this->object->setFieldName($field);
        $this->assertEquals($field, $this->object->getFieldName());
    }

    /**
     * @covers UserError::getFieldName
     */
    public function testGetFieldName() {
        $field = "company";
        $this->object->setFieldName($field);
        $this->assertEquals($field, $this->object->getFieldName());
    }

    /**
     * @covers UserError::setMessage
     */
    public function testSetMessage() {
        $msg = "This field is requires";
        $this->assertNull($this->object->getMessage());
        $this->object->setMessage($msg);
        $this->assertEquals($msg, $this->object->getMessage());
    }

    /**
     * @covers UserError::getMessage
     */
    public function testGetMessage() {
        $msg = "This field is requires";
        $this->object->setMessage($msg);
        $this->assertEquals($msg, $this->object->getMessage());
    }

    /**
     * @covers UserError::__toString
     */
    public function test__toString() {
        $msg = "This field is requires";
        $this->object->setMessage($msg);
        $this->assertEquals($msg, $this->object->__toString());
    }
}
?>
