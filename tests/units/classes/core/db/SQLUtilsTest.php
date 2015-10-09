<?php
/**
 * Created on May 28 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for SQLUtils.
 */
class SQLUtilsTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \SQLUtils
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \SQLUtils;
    }

    /**
     * @covers SQLUtils::escapeString
     */
    public function testEscapeStringNull() {
        $result = \SQLUtils::escapeString(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::escapeString
     */
    public function testEscapeString() {
        $str = 'string';
        $result = \SQLUtils::escapeString($str);
        $expected = "'string'";
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::escapeString
     */
    public function testEscapeStringWithoutQuotes() {
        $str = '';
        $result = \SQLUtils::escapeString($str, false);
        $expected = '';
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertDate
     */
    public function testConvertDateNull() {
        $result = \SQLUtils::convertDate(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertDate
     */
    public function testConvertDateNullWithoutQuotes() {
        $result = \SQLUtils::convertDate(null, false);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertDate
     */
    public function testConvertDate() {
        $dateTime = '1444395845';
        $result = \SQLUtils::convertDate($dateTime);
        $expected = "'2015-10-09 13:04:05'";
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertDate
     */
    public function testConvertDateWithoutQuotes() {
        $dateTime = '1444395845';
        $result = \SQLUtils::convertDate($dateTime, 'GMT', false);
        $expected = '2015-10-09 13:04:05';
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertTime
     */
    public function testConvertTimeNull() {
        $result = \SQLUtils::convertTime(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertTime
     */
    public function testConvertTimeEmpty() {
        $result = \SQLUtils::convertTime('');
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertTime
     */
    public function testConvertTime() {
        $time = '4952';
        $result = \SQLUtils::convertTime($time);
        $expected = "'01:22:32'";
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertTime
     */
    public function testConvertTimeWithoutQuotes() {
        $time = '4952';
        $result = \SQLUtils::convertTime($time, false);
        $expected = '01:22:32';
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertLong
     */
    public function testConvertLongNull() {
        $result = \SQLUtils::convertLong(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertLong
     */
    public function testConvertLongEmpty() {
        $result = \SQLUtils::convertLong('');
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertLong
     */
    public function testConvertLong() {
        $val = 7625149;
        $result = \SQLUtils::convertLong($val);
        $expected = 7625149;
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertDouble
     */
    public function testConvertDoubleNull() {
        $result = \SQLUtils::convertDouble(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertDouble
     */
    public function testConvertDoubleEmpty() {
        $result = \SQLUtils::convertDouble('');
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertDouble
     */
    public function testConvertDouble() {
        $val = 7625149.45;
        $result = \SQLUtils::convertDouble($val);
        $this->assertEquals($val, $result);
    }

    /**
     * @covers SQLUtils::convertId
     */
    public function testConvertIdNegative() {
        $result = \SQLUtils::convertId(-1);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertId
     */
    public function testConvertIdNegativeWithoutQuote() {
        $result = \SQLUtils::convertId(-1, false);
        $this->assertEquals(null, $result);
    }

    /**
     * @covers SQLUtils::convertId
     */
    public function testConvertIdNull() {
        $result = \SQLUtils::convertId(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertId
     */
    public function testConvertIdEmpty() {
        $result = \SQLUtils::convertId('');
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertId
     */
    public function testConvertId() {
        $val = 2382;
        $result = \SQLUtils::convertId($val);
        $this->assertEquals($val, $result);
    }

    /**
     * @covers SQLUtils::convertBoolean
     */
    public function testConvertBooleanNull() {
        $result = \SQLUtils::convertBoolean(null);
        $this->assertEquals(0, $result);
    }

    /**
     * @covers SQLUtils::convertBoolean
     */
    public function testConvertBooleanFalse() {
        $result = \SQLUtils::convertBoolean(false);
        $this->assertEquals(0, $result);
    }

    /**
     * @covers SQLUtils::convertBoolean
     */
    public function testConvertBooleanZero() {
        $result = \SQLUtils::convertBoolean(0);
        $this->assertEquals(0, $result);
    }

    /**
     * @covers SQLUtils::convertBoolean
     */
    public function testConvertBooleanTrue() {
        $result = \SQLUtils::convertBoolean(true);
        $this->assertEquals(1, $result);
    }

    /**
     * @covers SQLUtils::convertBoolean
     */
    public function testConvertBooleanString() {
        $result = \SQLUtils::convertBoolean('something');
        $this->assertEquals(1, $result);
    }

    /**
     * @covers SQLUtils::convertBoolean
     */
    public function testConvertBooleanTrueOne() {
        $result = \SQLUtils::convertBoolean(1);
        $this->assertEquals(1, $result);
    }

    /**
     * @covers SQLUtils::convertGeom
     */
    public function testConvertGeomNull() {
        $result = \SQLUtils::convertGeom(null);
        $this->assertEquals('null', $result);
    }

    /**
     * @covers SQLUtils::convertGeom
     */
    public function testConvertGeomQuestionMark() {
        $result = \SQLUtils::convertGeom('?');
        $expected = "GeomFromText(? ,4326)";
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::convertGeom
     */
    public function testConvertGeom() {
        $geom = new \GeomPolygon('POLYGON(( -120.4585148052187 38.48179461611015,
                                            -120.4580956500488 38.48154730571904,
                                            -120.4571270499661 38.48225003941639,
                                            -120.4585148052187 38.48179461611015))', 'wkt');
        $result = \SQLUtils::convertGeom($geom);
        $expected = "GeomFromText('POLYGON(( -120.4585148052187 38.48179461611015,
                                            -120.4580956500488 38.48154730571904,
                                            -120.4571270499661 38.48225003941639,
                                            -120.4585148052187 38.48179461611015))' ,4326)";
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers SQLUtils::referenceValues
     */
    public function testReferenceValues() {
        $arr = array('element1', 'element2');
        $result = \SQLUtils::referenceValues($arr);
        $expected = array('element1', 'element2');
        $this->assertEquals($expected, $result);
    }
}
?>