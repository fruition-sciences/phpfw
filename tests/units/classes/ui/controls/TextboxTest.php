<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Textbox.
 */
class TextboxTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Textbox::__construct
     */
    public function test__construct() {
        $this->text = new \Textbox("comment");
        $this->assertEquals("input", $this->text->getType());
        $this->assertEquals("text", $this->text->get("type"));
    }

}
