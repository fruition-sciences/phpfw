<?php
/**
 * Date: 05/05/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for ContentAttachment.
 */
class ContentAttachmentTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ContentAttachment
     */
    protected $object;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new \ContentAttachment();
    }

    /**
     * @covers ContentAttachment::setContent
     */
    public function testSetContent() {
        $content = "Notification content";
        $actual = $this->object->getContent();
        $this->assertNull($actual);
        $this->object->setContent($content);
        $actual = $this->object->getContent();
        $this->assertSame($content, $actual);
    }

    /**
     * @covers ContentAttachment::getContent
     */
    public function testGetContent() {
        $content = "Notification content";
        $this->object->setContent($content);
        $actual = $this->object->getContent();
        $this->assertSame($content, $actual);
    }

    /**
     * @covers ContentAttachment::setContentType
     */
    public function testSetContentType() {
        $contentType = "Notification content type";
        $actual = $this->object->getContentType();
        $this->assertNull($actual);
        $this->object->setContentType($contentType);
        $actual = $this->object->getContentType();
        $this->assertSame($contentType, $actual);
    }

    /**
     * @covers ContentAttachment::getContentType
     */
    public function testGetContentType() {
        $contentType = "Notification content type";
        $this->object->setContentType($contentType);
        $actual = $this->object->getContentType();
        $this->assertSame($contentType, $actual);
    }

    /**
     * @covers ContentAttachment::setFileName
     */
    public function testSetFileName() {
        $fileName = "File Name";
        $actual = $this->object->getFileName();
        $this->assertNull($actual);
        $this->object->setFileName($fileName);
        $actual = $this->object->getFileName();
        $this->assertSame($fileName, $actual);
    }

    /**
     * @covers ContentAttachment::getFileName
     */
    public function testGetFileName() {
        $fileName = "File Name";
        $this->object->setFileName($fileName);
        $actual = $this->object->getFileName();
        $this->assertSame($fileName, $actual);
    }
}
