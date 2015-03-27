<?php
/**
 * Date: 05/05/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for FileAttachment.
 */
class FileAttachmentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers FileAttachment::__construct
     */
    public function test__construct() {
        $this->object = new \FileAttachment("filePath");
        $this->assertInstanceOf("FileAttachment", $this->object);
    }

    /**
     * @covers FileAttachment::getFilePath
     */
    public function testGetFilePath() {
        $this->object = new \FileAttachment("filePath");
        $this->assertSame("filePath", $this->object->getFilePath());
    }
}
