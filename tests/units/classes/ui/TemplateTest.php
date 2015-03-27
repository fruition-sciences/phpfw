<?php
/**
 * Date: 28/04/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Template.
 */
class TemplateTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Template
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \Template(null);
    }

    /**
     * @covers Template::set
     * @covers Template::__construct
     */
    public function testSet() {
        $key = "class";
        $value = "errorCall";
        $this->object->set($key, $value);
        $this->assertSame($value, $this->object->get($key));
    }

    /**
     * @covers Template::get
     */
    public function testGet() {
        $key = "class";
        $value = "errorCall";
        $this->object->set($key, $value);
        $this->assertSame($value, $this->object->get($key));
    }

    /**
     * @covers Template::containsKey
     */
    public function testContainsKey() {
        $key = "class";
        $value = "errorCall";
        $this->object->set($key, $value);
        $this->assertTrue($this->object->containsKey($key));
    }

    /**
     * @covers Template::containsKey
     */
    public function testContainsKeyFail() {
        $key = "class";
        $this->assertFalse($this->object->containsKey($key));
    }
}
?>
