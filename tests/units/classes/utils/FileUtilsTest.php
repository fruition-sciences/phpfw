<?php
/**
* Created on May 29 2015
* @author Sidiki Coulibaly
*/
namespace tests\units;
/**
 * Test class for FileUtils.
 */
class FileUtilsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers FileUtils::getFileContent
     */
    public function testGetFileContent() {
        $content = \FileUtils::getFileContent("tests/config/menu.xml");
        $actual = explode("\n", $content)[0];
        $actual = trim($actual);
        $expected = '<?xml version="1.0"?>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers FileUtils::dirList
     */
    public function testDirList() {
        $content = \FileUtils::dirList(dirname(__DIR__));
        $this->assertNotEmpty($content);
    }

    /**
     * @covers FileUtils::fileList
     */
    public function testFileList() {
        $content = \FileUtils::fileList(__DIR__);
        $this->assertContains(basename(__FILE__), $content);
    }
    
    /**
     * @covers FileUtils::fileList
     */
    public function testFileListEmpty() {
    	$content = \FileUtils::fileList(__DIR__, 'xslt');
    	$this->assertEmpty($content);
    }

    /**
     * @covers FileUtils::convertBytes
     */
    public function testConvertBytes() {
        $actual = \FileUtils::convertBytes(45678, 5);
        $expected = array(44.60742, "KB");
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers FileUtils::existsInIncludePath
     */
    public function testExistsInIncludePath() {
        $actual = \FileUtils::existsInIncludePath("vendor/autoload.php");
        $this->assertTrue($actual);
    }
    
    /**
     * @covers FileUtils::existsInIncludePath
     */
    public function testExistsInIncludePathNotInc() {
    	$actual = \FileUtils::existsInIncludePath(__DIR__. '/'. basename(__FILE__));
    	$this->assertTrue($actual);
    }
    
    /**
     * @covers FileUtils::sanitizeFileName
     */
    public function testSanitizeFileName() {
        $fileName = "1416828831_037-L'èarkmead$*&,(à)_C8-/d.2014-10-28.NDVI.png";
        $actual = \FileUtils::sanitizeFileName($fileName);
        $expected = "1416828831_037-L---arkmead--------_C8--d.2014-10-28.NDVI.png";
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers FileUtils::removeFilesWithExtensionInFolder
     */
    public function testRemoveFilesWithExtensionInFolder() {
        $directoryPath = __DIR__ . '/';
        $extension = 'tif';
        $actual = \FileUtils::removeFilesWithExtensionInFolder($directoryPath, $extension);
        $this->assertEquals(0, $actual);
    }
}
?>
