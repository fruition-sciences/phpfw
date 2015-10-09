<?php
/**
 * Created on Mar 29 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for StringUtils.
 */
class StringUtilsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers StringUtils::arrayToString
     */
    public function testArrayToString() {
        $data = array("Benjamin", "Sidiki", "Leyla", 23, 24, "Malek");
        $actual = \StringUtils::arrayToString($data, ',', false);
        $expected = "Benjamin,Sidiki,Leyla,23,24,Malek";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::arrayToString
     */
    public function testArrayToStringEndSeparator() {
        $data = array("Benjamin", "Sidiki", "Leyla", 23, 24, "Malek");
        $actual = \StringUtils::arrayToString($data, ':', true);
        $expected = "Benjamin:Sidiki:Leyla:23:24:Malek:";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::implodeIgnoreNull
     */
    public function testImplodeIgnoreNull() {
        $data = array("Benjamin", "Sidiki", "Leyla", 23, 24, "Malek");
        $actual = \StringUtils::implodeIgnoreNull("==", $data);
        $expected = "Benjamin==Sidiki==Leyla==23==24==Malek";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::implodeIgnoreNull
     */
    public function testImplodeIgnoreNullWithNull() {
        $data = array("Benjamin", "Sidiki", null, 23, 24, null);
        $actual = \StringUtils::implodeIgnoreNull("==", $data);
        $expected = "Benjamin==Sidiki==23==24";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::truncateFilePath
     * @covers StringUtils::getPathDelimiter
     */
    public function testTruncateFilePath() {
        $path = "usr/local/bin/phpfile.php";
        $actual = \StringUtils::truncateFilePath($path, 20);
        $expected =".../bin/phpfile.php";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::truncateFilePath
     */
    public function testTruncateFilePathEmpty() {
        $path = "";
        $actual = \StringUtils::truncateFilePath($path, 20);
        $this->assertEmpty($actual);
    }

    /**
     * @covers StringUtils::truncateFilePath
     * @covers StringUtils::getPathDelimiter
     */
    public function testTruncateFilePathDashed() {
        $path = "usr\\local\\bin\\phpfile.php";
        $actual = \StringUtils::truncateFilePath($path, 20);
        $expected = "...\\bin\\phpfile.php";
        $this->assertEquals($expected, $actual);
    }


    /**
     * @covers StringUtils::truncateFilePath
     * @covers StringUtils::getPathDelimiter
     */
    public function testTruncateFilePathToShort() {
        $path = "usr/local/bin/phpfile.php";
        $actual = \StringUtils::truncateFilePath($path, 26);
        $this->assertEquals($path, $actual);
    }

    /**
     * @covers StringUtils::formatFileSize
     */
    public function testFormatFileSize() {
        $actual = \StringUtils::formatFileSize(2526);
        $expected = "2 KB";
        $this->assertEquals($expected, $actual);
        $actual = \StringUtils::formatFileSize(1026);
        $expected = "1 KB";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::formatFileSize
     */
    public function testFormatFileSizeFormat() {
        $actual = \StringUtils::formatFileSize(2526, '%01.5lf %s');
        $expected = "2.46680 KB";
        $this->assertEquals($expected, $actual);
        $actual = \StringUtils::formatFileSize(1026, '%01.5lf %s');
        $expected = "1.00195 KB";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::capitalizeFirstLetter
     */
    public function testCapitalizeFirstLetter() {
        $string = "fruition sciences";
        $actual = \StringUtils::capitalizeFirstLetter($string);
        $expected = "Fruition sciences";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers StringUtils::startsWith
     */
    public function testStartsWith() {
        $haystack = "fruition sciences";
        $needle = "fruit";
        $response = \StringUtils::startsWith($haystack, $needle);
        $this->assertTrue($response);
    }

    /**
     * @covers StringUtils::startsWith
     */
    public function testStartsWithFail() {
        $haystack = "fruition sciences";
        $needle = "Fruit";
        $response = \StringUtils::startsWith($haystack, $needle);
        $this->assertFalse($response);
    }
}
?>
