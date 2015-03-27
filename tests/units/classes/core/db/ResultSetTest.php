<?php
/**
 * created on Apr 22 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for \ResultSet.
 */
class ResultSetTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \ResultSet
     */
    protected $result;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->result = new \ResultSet(array());
    }

    /**
     * @covers ResultSet::getString
     * @covers ResultSet::__construct
     */
    public function testGetString() {
        $this->result = new \ResultSet(array(
            'category' => 'foo',
            'year' => '1979',
            'month' => '11',
        ));
        $actual = $this->result->getString('category');
        $excepted = 'foo';
        $this->assertSame($actual, $excepted);
    }

    /**
     * @covers ResultSet::setString
     */
    public function testSetString() {
        $myKey  = 'role';
        $myValue = 'Tester';
        $this->result->setString($myKey, $myValue);
        $actual = $this->result->getString($myKey);
        $this->assertSame($actual, $myValue);
    }

    /**
     * @covers ResultSet::getLong
     */
    public function testGetLong() {
        $myKey  = 'month';
        $myValue = 45879647;
        $this->result->setLong($myKey, $myValue);
        $actual = $this->result->getLong($myKey);
        $this->assertSame($actual, $myValue);
    }
    /**
     * @covers ResultSet::getLong
     */
    public function testGetLongNull() {
        $myKey  = 'month';
        $myValue = null;
        $this->result->setLong($myKey, $myValue);
        $actual = $this->result->getLong($myKey);
        $this->assertNull($actual);
    }

    /**
     * @covers ResultSet::getId
     */
    public function testGetId() {
        $myKey  = 'day';
        $myValue = 23;
        $this->result->setString($myKey, $myValue);
        $actual = $this->result->getId($myKey);
        $this->assertSame($actual, $myValue);
    }

    /**
     * @covers ResultSet::getId
     */
    public function testGetIdNull() {
        $id  = 'resultID';
        $myValue = null;
        $this->result->setLong($id, $myValue);
        $actual = $this->result->getId($id);
        $this->assertSame($actual, -1);
    }

    /**
     * @covers ResultSet::setLong
     * @todo useless i think.
     */
    public function testSetLong() {
        $key  = 'day';
        $long = null;
        $this->result->setLong($key, $long);
        $actual = $this->result->getLong($key);
        $this->assertSame($actual, $long);
    }

    /**
     * @covers ResultSet::getDouble
     */
    public function testGetDoubleNull() {
        $key  = 'day';
        $double = null;
        $this->result->setDouble($key, $double);
        $actual = $this->result->getDouble($key);
        $this->assertSame($actual, $double);
    }
    /**
     * @covers ResultSet::getDouble
     */
    public function testGetDouble() {
        $key  = 'day';
        $double = 23.24;
        $this->result->setDouble($key, $double);
        $actual = $this->result->getDouble($key);
        $this->assertSame($actual, $double);
    }

    /**
     * @covers ResultSet::setDouble
     */
    public function testSetDouble() {
        $key  = 'aDouble';
        $double = 17.75;
        $this->result->setDouble($key, $double);
        $actual = $this->result->getDouble($key);
        $this->assertSame($actual, $double);
    }

    /**
     * @covers ResultSet::getDate
     */
    public function testGetDate() {
        $dateKey  = 'aDate';
        $date = '2015-04-23 15:16:17';
        $this->result->setDate($dateKey, $date);
        $actual = $this->result->getDate($dateKey);
        $excepted = 1429802177;
        $this->assertSame($actual, $excepted);
    }
    /**
     * @covers ResultSet::getDate
     */
    public function testGetDateParisTimeZone() {
        $dateKey  = 'aDate';
        $date = '2015-04-23 15:16:17';
        $this->result->setDate($dateKey, $date);
        $actual = $this->result->getDate($dateKey, 'Europe/Paris');
        $excepted = 1429794977;
        $this->assertSame($actual, $excepted);
    }
    /**
     * @covers ResultSet::getDate
     */
    public function testGetDateNoTime() {
        $dateKey  = 'aDate';
        $date = '2015-04-23';
        $this->result->setDate($dateKey, $date);
        $actual = $this->result->getDate($dateKey);
        $excepted = 1429747200;
        $this->assertSame($actual, $excepted);
    }

    /**
     * @covers ResultSet::setDate
     */
    public function testSetDate() {
        $dateKey  = 'aDate';
        $date = '2015-04-23 15:16:17';
        $this->result->setDate($dateKey, $date);
        $actual = $this->result->getDate($dateKey);
        $this->assertNotNull($actual);
    }

    /**
     * @covers ResultSet::getTime
     */
    public function testGetTime() {
        $timeKey  = 'aTime';
        $time = '15:22:33';
        $this->result->setTime($timeKey, $time);
        $actual = $this->result->getTime($timeKey);
        $excepted = 55353;
        $this->assertSame($actual, $excepted);
    }

    /**
     * @covers ResultSet::setTime
     */
    public function testSetTime() {
        $timeKey  = 'aTime';
        $time = '16:02:15';
        $this->result->setTime($timeKey, $time);
        $actual = $this->result->getTime($timeKey);
        $this->assertNotNull($actual);
    }

    /**
     * @covers ResultSet::getPoint
     */
    public function testGetPoint() {
        $pointKey  = 'point';
        $geoPoint = 'POINT(-122.3340921148 38.421022632969)';
        $this->result->setString($pointKey, $geoPoint);
        $actual = $this->result->getPoint($pointKey)->toWKT();
        $excepted = \GeomPoint::fromWKT($geoPoint)->toWKT();
        $this->assertSame($actual, $excepted);
    }

    /**
     * @covers ResultSet::getPoint
     */
    public function testGetPointNull() {
        $pointKey  = 'point';
        $geoPoint = null;
        $this->result->setString($pointKey, $geoPoint);
        $actual = $this->result->getPoint($pointKey);
        $this->assertNull($actual);
    }

    /**
     * @covers ResultSet::getPolygon
     */
    public function testGetPolygon() {
        $polyKey  = 'polygon';
        $geoPolygon = 'POLYGON((1 1,5 1,5 5,1 5,1 1))';
        $this->result->setString($polyKey, $geoPolygon);
        $actual = $this->result->getPolygon($polyKey)->toWKT();
        $excepted = new \GeomPolygon($geoPolygon);
        $this->assertSame($actual, $excepted->toWKT());
    }

    /**
     * @covers ResultSet::getPolygon
     */
    public function testGetPolygonNull() {
        $polyKey  = 'polygon';
        $geoPolygon = null;
        $this->result->setString($polyKey, $geoPolygon);
        $actual = $this->result->getPolygon($polyKey);
        $this->assertNull($actual);
    }

    /**
     * @covers ResultSet::containsKey
     */
    public function testContainsKey() {
        $myKey = "myKey";
        $this->assertFalse($this->result->containsKey($myKey));
        $this->result->setString($myKey, "myString");
        $this->assertTrue($this->result->containsKey($myKey));
    }

    /**
     * @covers ResultSet::getAttributes
     */
    public function testGetAttributes() {
        $this->assertEmpty($this->result->getAttributes());
        $this->result = new \ResultSet(array(
            'category' => 'foo',
            'year' => '1979',
            'month' => '11',
        ));
        $attrs = $this->result->getAttributes();
        $this->assertArrayHasKey("year", $attrs);
    }
}
?>
