<?php
/**
 * Created on May 05 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Formatter.
 */
class FormatterTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Formatter
     */
    protected $formatter;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->formatter = new \Formatter("America/Los_Angeles");
    }

    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("America/Los_Angeles");
        $user->setLocale("en_US");
        $transaction->setUser($user);

        $zend_locale = new \Zend_Locale("en_US");
        \Zend_Registry::set('Zend_Locale', $zend_locale);
    }

    /**
     * @covers Formatter::getInstance
     * @covers Formatter::Formatter
     */
    public function testGetInstance() {
        $format = \Formatter::getInstance();
        $this->assertEquals($this->formatter, $format);
    }

    /**
     * @covers Formatter::date
     */
    public function testDate() {
        $actual = $this->formatter->date(1430469263, "2015-05-01");
        $expected = "5/01/15";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::date
     */
    public function testDateDefault() {
        $actual = $this->formatter->date(null, "2015-05-01");
        $expected = "2015-05-01";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::dateTime
     */
    public function testDateTime() {
        $actual = $this->formatter->dateTime(1430469263);
        $expected = "5/01/15 1:34 AM";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::dateTime
     */
    public function testDateTimeEmpty() {
        $actual = $this->formatter->dateTime(null, true, true);
        $this->assertEmpty($actual);
    }

    /**
     * @covers Formatter::dateTime24
     */
    public function testDateTime24() {
        $actual = $this->formatter->dateTime24(1430469263);
        $expected = "05/01/2015 01:34:23";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::time
     */
    public function testTime() {
        $actual = $this->formatter->time(1430469263, true);
        $expected = "1:34:23 AM";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::time
     */
    public function testTimeEmpty() {
        $actual = $this->formatter->time(null, true);
        $this->assertEmpty($actual);
    }

    /**
     * @covers Formatter::secondsToTime
     */
    public function testSecondsToTime() {
        $actual = $this->formatter->secondsToTime(3697);
        $expected = "01:01:37";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::dateFormat
     */
    public function testDateFormat() {
        $actual = $this->formatter->dateFormat(1430469263, "Y-m-d H:i:s");
        $expected = "2015-05-01 01:34:23";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::dateFormat
     */
    public function testDateFormatEmpty() {
        $actual = $this->formatter->dateFormat("", "Y-m-d H:i:s");
        $this->assertEmpty($actual);
    }

    /**
     * @covers Formatter::dateFormat
     */
    public function testDateFormatException() {
        $formatter = new \Formatter('Mars/Phobos', "fr_Fr");
        $actual = $formatter->dateFormat("1430469263", "Y-m-d H:i:s");
        $this->assertEmpty($actual);
    }

    /**
     * @covers Formatter::dateTimeUTC
     * @covers Formatter::getUTCTimeZone
     */
    public function testDateTimeUTC() {
        $actual = $this->formatter->dateTimeUTC(1430469263, "Y-m-d H:i:s");
        $expected = "2015-05-01 08:34:23 UTC";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::number
     */
    public function testNumber() {
        $actual = $this->formatter->number(14304, 4);
        $expected = "14,304.0000";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::getNumber
     */
    public function testGetNumber() {
        $actual = $this->formatter->getNumber("14,304", 1);
        $expected = "14304";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::getNumber
     */
    public function testGetNumberException() {
        $actual = $this->formatter->getNumber("Nan", 1);
        $this->assertFalse($actual);
    }

    /**
     * @covers Formatter::getTimeZone
     */
    public function testGetTimeZone() {
        $actual = $this->formatter->getTimeZone();
        $expected = "America/Los_Angeles";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::getLocale
     */
    public function testGetLocale() {
        $actual = $this->formatter->getLocale();
        $expected = new \Zend_Locale('en_US');;
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Formatter::getLocaleName
     */
    public function testGetLocaleName() {
        $actual = $this->formatter->getLocaleName();
        $expected = "en_US";
        $this->assertEquals($expected, $actual);
    }
}
?>
