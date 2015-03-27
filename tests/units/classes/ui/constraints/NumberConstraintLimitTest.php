<?php
namespace tests\units;
/**
 * Test class for NumberConstraintLimit.
 */
class NumberConstraintLimitTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers NumberConstraintLimit::__construct
     */
    public function test__construct() {
        $actual = new \NumberConstraintLimit(25);
        $this->assertEquals(25, $actual->getValue());
        $this->assertFalse($actual->isExclusive());
    }

    /**
     * @covers NumberConstraintLimit::__construct
     * @expectedException IllegalArgumentException
     * @expectedExceptionMessage value must be a number
     */
    public function test__constructException() {
        $actual = new \NumberConstraintLimit("number");
        $this->assertNull($actual);
    }

    /**
     * @covers NumberConstraintLimit::getValue
     */
    public function testGetValue() {
        $actual = new \NumberConstraintLimit(25);
        $this->assertEquals(25,  $actual->getValue());
    }

    /**
     * @covers NumberConstraintLimit::isExclusive
     */
    public function testIsExclusive() {
        $actual = new \NumberConstraintLimit(25);
        $this->assertFalse( $actual->isExclusive());
    }
}
