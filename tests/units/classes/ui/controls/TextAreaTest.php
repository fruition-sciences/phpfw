<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for TextArea.
 */
class TextAreaTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \TextArea
     */
    protected $text;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->text = new \TextArea("comment");
    }

    /**
     * @covers TextArea::__construct
     */
    public function test__construct() {
        $this->assertEquals("textarea", $this->text->getType());
    }

    /**
     * @covers TextArea::setValue
     */
    public function testSetValue() {
        $value = "June";
        $text = $this->text->setValue($value);
        $actual = $text->getBody();
        $this->assertEquals($value, $actual);
    }

    /**
     * @covers TextArea::toString
     */
    public function testToString() {
        $value = "June";
        $text = $this->text->setValue($value);
        $actual = $text->toString();
        $this->assertEquals($value, $actual);
    }
}
