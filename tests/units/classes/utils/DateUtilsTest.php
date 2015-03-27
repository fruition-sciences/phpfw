<?php
/**
 * Created on May 29 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for DateUtils.
 */
class DateUtilsTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var DateUtils
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \DateUtils;
    }

    public static function setUpBeforeClass() {
        $transaction = \Transaction::getInstance();
        $user = new \User();
        $user->setTimezone("Europe/Paris");
        $transaction->setUser($user);

        $zend_locale = new \Zend_Locale("en_US");
        \Zend_Registry::set('Zend_Locale', $zend_locale);
    }

    /**
     * @covers DateUtils::getHourOfDay
     */
    public function testGetHourOfDay() {
        $time = 1430388542;
        $actual = \DateUtils::getHourOfDay($time, "Europe/Paris");
        $excepted = 12;
        $this->assertEquals($excepted, $actual);
        $nextWeek = $time + (7 * 24 * 60 * 60);
        $actual = \DateUtils::getHourOfDay($nextWeek, "Europe/Paris");
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers DateUtils::getHourOfDay
     */
    public function testGetHourOfDayLATimezone() {
        $time = 1430388542;
        $actual = \DateUtils::getHourOfDay($time, "America/Los_Angeles");
        $execpted = 3;
        $this->assertEquals($execpted, $actual);
        $nextWeek = $time + (7 * 24 * 60 * 60);
        $actual = \DateUtils::getHourOfDay($nextWeek, "America/Los_Angeles");
        $excepted = 3;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::addDays
     */
    public function testAddDays() {
        $time = 1430388542;
        $actual = \DateUtils::addDays($time, 2, null, 30, null, "Europe/Paris");
        $excepted = 1430562602;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::addDays
     */
    public function testAddDaysNoTimeZone() {
        $time = 1430388542;
        $actual = \DateUtils::addDays($time, 2, null, 30, null);
        $excepted = 1430562602;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::addDays
     */
    public function testAddDaysNohour() {
        $time = 1430388542;
        $actual = \DateUtils::addDays($time, 2, null, null, null, "Europe/Paris");
        $excepted = 1430561342;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::addDays
     */
    public function testAddDaysWithHour() {
        $time = 1430388542;
        $actual = \DateUtils::addDays($time, 2, 2, null, null, "Europe/Paris");
        $excepted = 1430525342;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::addDays
     */
    public function testAddDaysWithMinutes() {
        $time = 1430388542;
        $actual = \DateUtils::addDays($time, 2, 2, 30, null,"Europe/Paris");
        $excepted = 1430526602;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::addDays
     */
    public function testAddDaysWithSecondes() {
        $time = 1430388542;
        $actual = \DateUtils::addDays($time, 2, 2, 30, 45,"Europe/Paris");
        $excepted = 1430526645;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::add
     */
    public function testAddHour() {
        $time = 1430388542;
        $actual = \DateUtils::add($time, "hour", 2, "Europe/Paris");
        $excepted = 1430395742;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::add
     */
    public function testAddDay() {
        $time = 1430388542;
        $actual = \DateUtils::add($time, "day", 8, "Europe/Paris");
        $excepted = 1431079742;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::add
     */
    public function testAddMonth() {
        $time = 1430388542;
        $actual = \DateUtils::add($time, "month", 12, "Europe/Paris");
        $excepted = 1462010942;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::add
     */
    public function testAddYear() {
        $time = 1430388542;
        $actual = \DateUtils::add($time, "year", 1, "Europe/Paris");
        $excepted = 1462010942;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::getBeginningOfDay
     */
    public function testGetBeginningOfDay() {
        $time = 1430388542;
        $actual = \DateUtils::getBeginningOfDay($time, "Europe/Paris");
        $excepted = 1430344800;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::getBeginningOfPreviousDay
     */
    public function testGetBeginningOfPreviousDay() {
        $time = 1430388542;
        $actual = \DateUtils::getBeginningOfPreviousDay($time, "Europe/Paris");
        $excepted = 1430258400;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::getBeginningOfWeek
     */
    public function testGetBeginningOfWeek() {
        $time = 1430388542;
        $actual = \DateUtils::getBeginningOfWeek($time, "Europe/Paris");
        $excepted = 1430085600;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::getBeginningOfMonth
     */
    public function testGetBeginningOfMonth() {
        $time = 1430388542;
        $actual = \DateUtils::getBeginningOfMonth($time, "America/Los_Angeles");
        $excepted = 1427871600;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::timeDiff
     */
    public function testTimeDiff() {
        $startTime = 1430388542;
        $end = 1430403672;
        $actual = \DateUtils::timeDiff($startTime, $end);
        $excepted = "04:12:10";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::timeDiff
     */
    public function testTimeDiffLess() {
        $startTime = 1430403672;
        $end = 1430388542;
        $actual = \DateUtils::timeDiff($startTime, $end);
        $excepted = "-04:12:10";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::modifyTimestamp
     */
    public function testModifyTimestampTimeZone() {
        $time = 1430403672;
        $actual = \DateUtils::modifyTimestamp($time, "+1 day", "America/Los_Angeles");
        $excepted = "1430490072";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::modifyTimestamp
     */
    public function testModifyTimestamp() {
        $time = 1430403672;
        $actual = \DateUtils::modifyTimestamp($time, "+1 day");
        $excepted = "1430490072";
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::getMonthsArray
     */
    public function testGetMonthsArray() {
        $actual = \DateUtils::getMonthsArray("wide");
        $excepted = Array( "1" => "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers DateUtils::makeDate
     */
    public function testMakeDate() {
        $date = \DateUtils::makeDate(2015, 5, 1, 10, 34, 23, "Europe/Paris");
        $this->assertInstanceOf("DateTime", $date);
        $actual = $date->getTimestamp();
        $excepted = 1430469263;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::makeDateFromTimestamp
     */
    public function testMakeDateFromTimestamp() {
        $time = 1430469263;
        $date = \DateUtils::makeDateFromTimestamp($time, "Europe/Paris");
        $this->assertInstanceOf("DateTime", $date);
        $actual = $date->getTimestamp();
        $excepted = 1430469263;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::makeDateFromTimestamp
     */
    public function testMakeDateFromTimestampNoTimeZone() {
        $time = 1430469263;
        $date = \DateUtils::makeDateFromTimestamp($time);
        $this->assertInstanceOf("DateTime", $date);
        $actual = $date->getTimestamp();
        $excepted = 1430469263;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::dateTimeToTimestamp
     */
    public function testDateTimeToTimestamp() {
        $timeZone = new \DateTimeZone("Europe/Paris");
        $dateTime = new \DateTime("2015-05-01 10:34:23", $timeZone);
        $actual  = \DateUtils::dateTimeToTimestamp($dateTime);
        $excepted = 1430469263;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::getGmtOffset
     */
    public function testGetGmtOffset() {
        $time = 1430469263;
        $actual = \DateUtils::getGmtOffset($time, "Europe/Paris");
        $excepted = 7200;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::isSameDay
     */
    public function testIsSameDay() {
        $time1 = 1430469263;
        $time2 = 1430388542;
        $actual = \DateUtils::isSameDay($time1, $time2, "Europe/Paris");
        $this->assertFalse($actual);
    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInInterval() {
        $hour = 11;
        $minute = 45;
        $lowerHour = 11;
        $lowerMinute = 32;
        $upperHour = 13;
        $upperMinute = 23;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertTrue($actual);

    }

    /**
    * @covers DateUtils::timeInInterval
    */
    public function testTimeInIntervalFalse() {
        $hour = 15;
        $minute = 30;
        $lowerHour = 8;
        $lowerMinute = 32;
        $upperHour = 13;
        $upperMinute = 23;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertFalse($actual);

    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInIntervalSameHour() {
        $hour = 13;
        $minute = 30;
        $lowerHour = 8;
        $lowerMinute = 32;
        $upperHour = 13;
        $upperMinute = 23;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertFalse($actual);

    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInIntervalLowerMinute() {
        $hour = 13;
        $minute = 10;
        $lowerHour = 13;
        $lowerMinute = 32;
        $upperHour = 14;
        $upperMinute = 23;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertFalse($actual);
    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInIntervalUpperMinute() {
        $hour = 10;
        $minute = 20;
        $lowerHour = 20;
        $lowerMinute = 55;
        $upperHour = 20;
        $upperMinute = 45;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertTrue($actual);
    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInIntervalUpperHour() {
        $hour = 12;
        $minute = 20;
        $lowerHour = 22;
        $lowerMinute = 15;
        $upperHour = 22;
        $upperMinute = 45;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertFalse($actual);
    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInIntervalSameLowAndUpper() {
        $hour = 22;
        $minute = 10;
        $lowerHour = 22;
        $lowerMinute = 15;
        $upperHour = 22;
        $upperMinute = 45;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertFalse($actual);
    }

    /**
     * @covers DateUtils::timeInInterval
     */
    public function testTimeInIntervalbigerLower() {
        $hour = 22;
        $minute = 10;
        $lowerHour = 22;
        $lowerMinute = 15;
        $upperHour = 20;
        $upperMinute = 45;
        $actual = \DateUtils::timeInInterval($hour, $minute, $lowerHour, $lowerMinute, $upperHour, $upperMinute);
        $this->assertFalse($actual);
    }

    /**
     * @covers DateUtils::excelToTimestamp
     */
    public function testExcelToTimestamp() {
        $actual = \DateUtils::excelToTimestamp(0);
        $excepted = 1430870400;

        $this->assertGreaterThan($excepted, $actual);
    }

    /**
     * @covers DateUtils::excelToTimestamp
     */
    public function testExcelToTimestampMacDate() {
        $actual = \DateUtils::excelToTimestamp(40932, true);
        $excepted = 1453680000;
        $this->assertSame($excepted, $actual);
    }

    /**
     * @covers DateUtils::parseDate
     */
    public function testParseDate() {
        $actual = \DateUtils::parseDate("2015-05-01 10:34:23", "Europe/Paris");
        $excepted = 1430469263;
        $this->assertEquals($excepted, $actual);
    }
}
?>
