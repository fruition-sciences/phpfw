<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Hidden.
 */
class HiddenTest extends \PHPUnit_Framework_TestCase {
    /**
     * @covers Hidden::__construct
     */
    public function test__construct() {
        $hidden = new \Hidden("sensors");
        $this->assertSame("input", $hidden->getType());
        $this->assertSame("hidden", $hidden->get("type"));
    }

    /**
     * @covers Hidden::toString
     */
    public function testToString() {
        $hidden = new \Hidden("sensors");
        $this->assertEmpty($hidden->toString());
    }
}
