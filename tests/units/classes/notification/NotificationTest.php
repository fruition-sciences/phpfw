<?php
/**
 * Date: 05/04/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Notification.
 */
class NotificationTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Notification
     */
    protected $notification;
    
    /**
     * @var INotificationManager
     */
    private $notificationManager;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->notification = new \Notification();
    }

    /**
     * @covers Notification::setRecipient
     */
    public function testSetRecipient() {
        $actual = $this->notification->getRecipient();
        $this->assertNull($actual);
        $this->notification->setRecipient("user@email.com, sender@email.fr");
        $actual = $this->notification->getRecipient();
        $this->assertNotEmpty($actual);
    }

    /**
     * @covers Notification::getRecipient
     */
    public function testGetRecipient() {
        $this->notification->setRecipient("user@email.com, sender@email.fr");
        $actual = $this->notification->getRecipient();
        $this->assertNotEmpty($actual);
    }

    /**
     * @covers Notification::addCC
     */
    public function testAddCC() {
        $actual = $this->notification->getCCList();
        $this->assertEmpty($actual);
        $this->notification->addCC("user@email.com");
        $actual = $this->notification->getCCList();
        $this->assertContains("user@email.com", $actual);
    }

    /**
     * @covers Notification::getCCList
     */
    public function testGetCCList() {
        $this->notification->addCC("user@email.com");
        $actual = $this->notification->getCCList();
        $this->assertContains("user@email.com", $actual);
    }

    /**
     * @covers Notification::addBCC
     */
    public function testAddBCC() {
        $actual = $this->notification->getBCCList();
        $this->assertEmpty($actual);
        $this->notification->addBCC("user@email.com");
        $actual = $this->notification->getBCCList();
        $this->assertContains("user@email.com", $actual);
    }

    /**
     * @covers Notification::getBCCList
     */
    public function testGetBCCList() {
        $this->notification->addBCC("user@email.com");
        $actual = $this->notification->getBCCList();
        $this->assertContains("user@email.com", $actual);
    }

    /**
     * @covers Notification::setFrom
     */
    public function testSetFrom() {
        $actual = $this->notification->getFrom();
        $this->assertNull($actual);
        $this->notification->setFrom("sender@email.fr");
        $actual = $this->notification->getFrom();
        $this->assertSame("sender@email.fr", $actual);
    }

    /**
     * @covers Notification::getFrom
     */
    public function testGetFrom() {
        $this->notification->setFrom("sender@email.fr");
        $actual = $this->notification->getFrom();
        $this->assertSame("sender@email.fr", $actual);
    }

    /**
     * @covers Notification::setSubject
     */
    public function testSetSubject() {
        $actual = $this->notification->getSubject();
        $this->assertNull($actual);
        $this->notification->setSubject("Conversation");
        $actual = $this->notification->getSubject();
        $this->assertEquals("Conversation", $actual);
    }

    /**
     * @covers Notification::getSubject
     */
    public function testGetSubject() {
        $this->notification->setSubject("Conversation");
        $actual = $this->notification->getSubject();
        $this->assertEquals("Conversation", $actual);
    }

    /**
     * @covers Notification::setContent
     */
    public function testSetContent() {
        $actual = $this->notification->getContent();
        $this->assertNull($actual);
        $content = "Conversation content";
        $this->notification->setContent($content);
        $actual = $this->notification->getContent();
        $this->assertEquals($content, $actual);
    }

    /**
     * @covers Notification::getContent
     * @todo Implement testGetContent().
     */
    public function testGetContent() {
        $content = "Conversation content";
        $this->notification->setContent($content);
        $actual = $this->notification->getContent();
        $this->assertEquals($content, $actual);
    }

    /**
     * @covers Notification::setFormat
     */
    public function testSetFormat() {
        $actual = $this->notification->getFormat();
        $this->assertNull($actual);
        $format = \Notification::FORMAT_HTML;
        $this->notification->setFormat($format);
        $actual = $this->notification->getFormat();
        $this->assertEquals($format, $actual);
    }

    /**
     * @covers Notification::getFormat
     */
    public function testGetFormat() {
        $format = \Notification::FORMAT_HTML;
        $this->notification->setFormat($format);
        $actual = $this->notification->getFormat();
        $this->assertEquals($format, $actual);
    }

    /**
     * @covers Notification::attachContent
     */
    public function testAttachContent() {
        $actual = $this->notification->getAttachments();
        $this->assertEmpty($actual);
        $this->notification->attachContent("Blob content", "text", "fileName");
        $actual = $this->notification->getAttachments();
        $this->assertNotEmpty($actual);
        $this->assertContainsOnly("ContentAttachment", $actual);
    }

    /**
     * @covers Notification::attachFile
     */
    public function testAttachFile() {
        $actual = $this->notification->getAttachments();
        $this->assertEmpty($actual);
        $this->notification->attachFile("fileName");
        $actual = $this->notification->getAttachments();
        $this->assertNotEmpty($actual);
        $this->assertContainsOnly("FileAttachment", $actual);
    }

    /**
     * @covers Notification::getAttachments
     */
    public function testGetAttachments() {
        $this->notification->attachFile("fileName");
        $actual = $this->notification->getAttachments();
        $this->assertNotEmpty($actual);
    }

    /**
     * @covers Notification::setNotificationManager
     */
    public function testSetNotificationManager() {
        $actual = $this->notification->getNotificationManager();
        $this->assertNull($actual);
        $manager = new \NotificationManager();
        $this->notification->setNotificationManager($manager);
        $actual = $this->notification->getNotificationManager();
        $this->assertSame($manager, $actual);
    }

    /**
     * @covers Notification::getNotificationManager
     */
    public function testGetNotificationManager() {
        $manager = new \NotificationManager();
        $this->notification->setNotificationManager($manager);
        $actual = $this->notification->getNotificationManager();
        $this->assertSame($manager, $actual);
    }
}
?>
