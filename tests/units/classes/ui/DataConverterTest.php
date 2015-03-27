<?php
/**
 * Created on May 07 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for MeasureTextbox.
 */
class DataConverterTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \DataConverter
     */
    protected $converter;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->converter = \DataConverter::getInstance();;
    }

    /**
     * This method is called before the first test is executed.
     */
    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("Europe/Paris");
        $transaction->setUser($user);

        $zend_locale = new \Zend_Locale("en_US");
        \Zend_Registry::set('Zend_Locale', $zend_locale);
    }

    /**
     * @covers DataConverter::parseDate
     * @covers DataConverter::DataConverter
     * @covers DataConverter::getInstance
     * @covers DataConverter::getDatePattern
     */
    public function testParseDate() {
        $actual = $this->converter->parseDate("05/25/2015");
        $expected = 1432504800;
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers DataConverter::parseDate
     * @covers DataConverter::parseDateByPattern
     */
    public function testParseDatePattern() {
        $actual = $this->converter->parseDate("2015/05/25", "yyyy.MM.dd");
        $expected = 1432504800;
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers DataConverter::parseDate
     * @covers DataConverter::parseDateByPattern
     */
    public function testParseDateEmpty() {
        $actual = $this->converter->parseDate("", "yyyy.MM.dd");
        $this->assertNull($actual);
    }

    /**
     * @covers DataConverter::parseTime
     */
    public function testParseTime() {
        $actual = $this->converter->parseTime("15:05:11");
        $expected = 54311;
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers DataConverter::getTimeZoneName
     */
    public function testGetTimeZoneName() {
        $actual = $this->converter->getTimeZoneName();
        $expected = "Europe/Paris";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers DataConverter::getDatePattern
     */
    public function testGetDatePattern() {
        $actual = \DataConverter::getDatePattern("Europe/Paris", false, true);
        $expected = "h:mm a";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers DataConverter::getDatePattern
     * @expectedException IllegalArgumentException
     * @expectedExceptionMessage Date format must contain date or/and time
     */
    public function testGetDatePatternException() {
        $actual = \DataConverter::getDatePattern("Europe/Paris", false, false);
        $expected = "h:mm a";
        $this->assertEquals($expected, $actual);
    }
}
