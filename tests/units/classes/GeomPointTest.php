<?php
/**
 * Created on Mar 29 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for GeomPoint.
 */
class GeomPointTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers GeomPoint::fromXY
     * @covers GeomPoint::__construct
     */
    public function testFromXY() {
        $x = 12.32;
        $y = 10;
        $point = \GeomPoint::fromXY($x, $y);
        $this->assertEquals($x, $point->getX());
        $this->assertEquals($y, $point->getY());
    }

    /**
     * @covers GeomPoint::fromWKT
     */
    public function testFromWKT() {
        $point = \GeomPoint::fromWKT("POINT(30.22 -122.3340921148)");
        $this->assertInstanceOf("GeomPoint", $point);
        $this->assertEquals(30.22, $point->getX());
        $this->assertEquals(-122.3340921148, $point->getY());
    }

    /**
     * @covers GeomPoint::fromWKT
     */
    public function testFromWKTEmpty() {
        $point = \GeomPoint::fromWKT("");
        $this->assertNull($point);
    }

    /**
     * @covers GeomPoint::fromWKT
     * @expectedException IllegalArgumentException
     * @expectedExceptionMessage POIN(30.22 -122.3340921148)
     */
    public function testFromWKTException() {
        $point = \GeomPoint::fromWKT("POIN(30.22 -122.3340921148)");
        $this->assertNull($point);
    }

    /**
     * @covers GeomPoint::toWKT
     */
    public function testToWKT() {
        $point1 = \GeomPoint::fromWKT("POINT(30.22 -122.3340921148)");
        $point2 = \GeomPoint::fromXY(21, 3.32);
        $this->assertEquals("POINT(30.22 -122.3340921148)", $point1->toWKT());
        $this->assertEquals("POINT(21 3.32)", $point2->toWKT());
    }

    /**
     * @covers GeomPoint::toWKT
     * @expectedException IllegalArgumentException
     * @expectedExceptionMessage Invalid POINT format: POINT(30.22-122.3340921148)
     */
    public function testToWKTException() {
        $point = \GeomPoint::fromWKT("POINT(30.22-122.3340921148)");
    }

    /**
     * @covers GeomPoint::__toString
     */
    public function test__toString() {
        $point = \GeomPoint::fromXY(21, 3.32);
        $this->assertEquals("POINT(21 3.32)", $point->__toString());
    }

    /**
     * @covers GeomPoint::getX
     */
    public function testGetX() {
        $x = 23;
        $point = \GeomPoint::fromXY($x, 3.32);
        $this->assertEquals($x, $point->getX());
    }

    /**
     * @covers GeomPoint::getY
     */
    public function testGetY() {
        $y = 23;
        $point = \GeomPoint::fromXY(3.32, $y);
        $this->assertEquals($y, $point->getY());
    }

    /**
     * @covers GeomPoint::getHemisphere
     */
    public function testGetHemisphere() {
        $point = \GeomPoint::fromXY(21, 3.32);
        $this->assertEquals(\GeomPoint::NORTHERN_HEMISPHERE, $point->getHemisphere());
        $point = \GeomPoint::fromXY(21, -3.32);
        $this->assertEquals(\GeomPoint::SOUTHERN_HEMISPHERE, $point->getHemisphere());
    }
}
?>
