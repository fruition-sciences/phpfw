<?php
/**
 * Created on Mar 29 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for GeomPolygon.
 */
class GeomPolygonTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers GeomPolygon::__construct
     */
    public function test__construct() {
        $polygon = new \GeomPolygon("POLYGON ((30 10, 40 40, 20 40, 10 20, 30 10))");
        $this->assertEquals("POLYGON ((30 10, 40 40, 20 40, 10 20, 30 10))", $polygon);
    }

    /**
     * @covers GeomPolygon::__construct
     * @expectedException IllegalArgumentException
     */
    public function test__constructException() {
        $polygon = new \GeomPolygon("");
        $this->assertNull($polygon);
    }

    /**
     * @covers GeomPolygon::toWKT
     */
    public function testToWKT() {
        $polygon = new \GeomPolygon("POLYGON ((30 10, 40 40, 20 40, 10 20, 30 10))");
        $this->assertEquals("POLYGON ((30 10, 40 40, 20 40, 10 20, 30 10))", $polygon->toWKT());
    }

    /**
     * @covers GeomPolygon::__toString
     * @todo Implement test__toString().
     */
    public function test__toString() {
        $polygon = new \GeomPolygon("POLYGON ((30 10, 40 40, 20 40, 10 20, 30 10))");
        $this->assertEquals("POLYGON ((30 10, 40 40, 20 40, 10 20, 30 10))", $polygon->__toString());
    }
}
?>
