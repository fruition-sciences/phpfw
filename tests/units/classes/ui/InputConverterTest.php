<?php
/**
 * Date: 05/04/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for InputConverter.
 */
class InputConverterTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \InputConverter
     */
    protected $converter;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->converter = new \InputConverter("America/Los_Angeles", "en_US");
    }

    /**
     * This method is called before the first test is executed.
     */
    public static function setUpBeforeClass() {
        $zend_locale = new \Zend_Locale("en_US");
        \Zend_Registry::set('Zend_Locale', $zend_locale);
    }

    /**
     * @covers InputConverter::getDate
     * @covers InputConverter::__construct
     * @covers InputConverter::getValue
     */
    public function testGetDate() {
        $map = array('today' => "05-01-2015 10:34:23");
        $actual = $this->converter->getDate($map, "today");
        $excepted = 1430463600;
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers InputConverter::getDate
     * @covers InputConverter::__construct
     * @covers InputConverter::getValue
     */
    public function testGetDateEmpty() {
        $map = array('today' => "");
        $actual = $this->converter->getDate($map, "today");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setDate
     */
    public function testSetDate() {
        $map =  array();
        $this->converter->setDate($map, "today", 1430468663);
        $excepted = array("today"=> "5/01/15");
        $this->assertEquals($excepted, $map);
    }

    /**
     * @covers InputConverter::setDateTime
     */
    public function testSetDateTime() {
        $map =  array();
        $this->converter->setDateTime($map, "today", 1430468663);
        $excepted = array("today"=> "05/01/2015 01:24:23");
        $this->assertEquals($excepted, $map);
    }

    /**
     * @covers InputConverter::getTime
     */
    public function testGetTime() {
        $map = array('today' => "10:34:23");
        $actual = $this->converter->getTime($map, "today");
        $excepted = 38063;
        $this->assertEquals($excepted, $actual);
    }
    /**
     * @covers InputConverter::getTime
     */
    public function testGetTimeEmpty() {
        $map = array('today' => "");
        $actual = $this->converter->getTime($map, "today");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setTime
     */
    public function testSetTime() {
        $map = array();
        $actual = $this->converter->setTime($map, "time", 38063);
        $excepted = array("time" => "10:34:23");
        $this->assertEquals($excepted, $map);
    }

    /**
     * @covers InputConverter::getMeasure
     */
    public function testGetMeasure() {
        $map = array("temp" => 13, "temp__unit"=>"Zend_Measure_Temperature::CELSIUS");
        $actual = $this->converter->getMeasure($map, "temp");
        $this->assertInstanceOf("Zend_Measure_Temperature", $actual);
    }

    /**
     * @covers InputConverter::getMeasure
     */
    public function testGetMeasureNull() {
        $map = array("temp" => null, "temp__unit"=>"Zend_Measure_Temperature::CELSIUS");
        $actual = $this->converter->getMeasure($map, "temp");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setMeasure
     * @todo Implement testSetMeasure().
     */
    public function testSetMeasure() {
        $measure = new \Zend_Measure_Length(100,'METER');
        $key = "mykey";
        $map = array();
        $this->converter->setMeasure($map, $key, $measure);
        $excepted = array($key=> 100, $key. "__unit"=>"Zend_Measure_Length::METER", $key. "__measure"=>$measure);
        $this->assertEquals($excepted, $map);
    }

    /**
     * @covers InputConverter::getId
     */
    public function testGetId() {
        $id = 12;
        $map = array('key' => $id);
        $actual = $this->converter->getId($map, "key");
        $this->assertEquals($id, $actual);
    }

    /**
     * @covers InputConverter::getId
     */
    public function testGetIdNul() {
        $map = array('key' => null);
        $actual = $this->converter->getId($map, "key");
        $this->assertNull($actual);
    }


    /**
     * @covers InputConverter::setId
     * @todo Implement testSetId().
     */
    public function testSetId() {
        $id = 12;
        $map = array();
        $this->converter->setId($map, "key", $id);
        $expected = array("key"=>12);
        $this->assertEquals($expected, $map);
    }

    /**
     * @covers InputConverter::getLong
     */
    public function testGetLong() {
        $id = 12208;
        $map = array('key' => $id);
        $actual = $this->converter->getLong($map, "key");
        $this->assertEquals($id, $actual);
    }

    /**
     * @covers InputConverter::getLong
     */
    public function testGetLongNull() {
        $map = array('key' => null);
        $actual = $this->converter->getLong($map, "key");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setLong
     * @todo I didn't understand this function utility
     */
    public function testSetLong() {
        $id = 12679;
        $map = array();
        $this->converter->setLong($map, "key", $id);
        $expected = array("key"=>"12,679");
        $this->assertEquals($expected, $map);
    }

    /**
     * @covers InputConverter::getDouble
     */
    public function testGetDouble() {
        $id = 12.21;
        $map = array('key' => $id);
        $actual = $this->converter->getDouble($map, "key");
        $this->assertEquals($id, $actual);
    }

    /**
     * @covers InputConverter::getDouble
     */
    public function testGetDoubleNull() {
        $map = array('key' => null);
        $actual = $this->converter->getDouble($map, "key");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setDouble
     */
    public function testSetDouble() {
        $id = 12.679;
        $map = array();
        $this->converter->setDouble($map, "key", $id);
        $expected = array("key"=>$id);
        $this->assertEquals($expected, $map);
    }

    /**
     * @covers InputConverter::getString
     */
    public function testGetString() {
        $id = "Dashboard";
        $map = array('key' => $id);
        $actual = $this->converter->getString($map, "key");
        $this->assertEquals($id, $actual);
    }

    /**
     * @covers InputConverter::getString
     */
    public function testGetStringNull() {
        $id = "";
        $map = array('key' => $id);
        $actual = $this->converter->getString($map, "key");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setString
     */
    public function testSetString() {
        $str = "Dashboard";
        $map = array();
        $this->converter->setString($map, "key", $str);
        $expected = array("key"=>$str);
        $this->assertEquals($expected, $map);
    }

    /**
     * @covers InputConverter::getBoolean
     */
    public function testGetBoolean() {
        $bool = true;
        $map = array('key' => $bool);
        $actual = $this->converter->getBoolean($map, "key");
        $this->assertTrue($actual);
    }

    /**
     * @covers InputConverter::getBoolean
     */
    public function testGetBooleanNull() {
        $bool = null;
        $map = array('key' => $bool);
        $actual = $this->converter->getBoolean($map, "key");
        $this->assertNull($actual);
    }

    /**
     * @covers InputConverter::setBoolean
     */
    public function testSetBoolean() {
        $bool = true;
        $map = array();
        $this->converter->setBoolean($map, "key", $bool);
        $expected = array("key"=>$bool);
        $this->assertEquals($expected, $map);
    }

    /**
     * @covers InputConverter::setPoint
     * @covers InputConverter::getPoint
     */
    public function testSetPoint() {
        $point =  \GeomPoint::fromXY(12, 21);
        $map = array();
        $this->converter->setPoint($map, "key", $point);
        $expected = array("key_X"=>12, "key_Y"=>21, "key"=>$point);
        $this->assertEquals($expected, $map);
        $actual = $this->converter->getPoint($map, "key");
        $this->assertEquals($point, $actual);
    }

    /**
     * @covers InputConverter::getPoint
     * @covers InputConverter::getValue
     */
    public function testGetPointNull() {
        $map = array();
        $actual = $this->converter->getPoint($map, "key");
        $this->assertNull($actual);
    }


    /**
     * @covers InputConverter::setPolygon
     * @covers InputConverter::getPolygon
     */
    public function testSetPolygon() {
        $wkt = "POLYGON((1 1,5 1,5 5,1 5,1 1))";
        $polygon =  new \GeomPolygon($wkt);
        $map = array();
        $this->converter->setPolygon($map, "key", $polygon);
        $expected = array("key"=>$wkt);
        $this->assertEquals($expected, $map);
        $actual = $this->converter->getPolygon($map, "key");
        $this->assertEquals($polygon, $actual);
    }

    /**
     * @covers InputConverter::getPolygon
     * @covers InputConverter::getValue
     */
    public function testGetPolygonNull() {
        $map = array();
        $actual = $this->converter->getPolygon($map, "key");
        $this->assertNull($actual);
    }
}
?>
