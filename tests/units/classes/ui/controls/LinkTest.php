<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Link.
 */
class LinkTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Link
     */
    protected $link;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $href = $this->getMockBuilder('\Href')
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->link = new \Link($href);
    }

    /**
     * @covers Link::__construct
     */
    public function test__construct() {
        $this->assertEquals("a", $this->link->getType());
    }

    /**
     * @covers Link::setTitle
     */
    public function testSetTitle() {
        $title = "fruition sciences";
        $link = $this->link->setTitle($title);
        $actual =  $link->__toString();
        $excepted = '<a href="">fruition sciences</a>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Link::setParam
     */
    public function testSetParam() {
        $link = $this->link->setParam("id", 174);
        $this->assertInstanceOf("Link", $link);
    }

    /**
     * @covers Link::setAction
     */
    public function testSetAction() {
        $link = $this->link->setAction("goto");
        $this->assertInstanceOf("Link", $link);
    }

    /**
     * @covers Link::setAnchor
     */
    public function testSetAnchor() {
        $link = $this->link->setAnchor("anchor");
        $this->assertInstanceOf("Link", $link);
    }

    /**
     * @covers Link::__toString
     */
    public function test__toString() {
        $title = "fruition sciences";
        $link = $this->link->setTitle($title);
        $actual =  $link->__toString();
        $excepted = '<a href="">fruition sciences</a>';
        $this->assertEquals($excepted, $actual);
    }

    /**
     * @covers Link::getHref
     */
    public function testGetHref() {
        $href = $this->link->getHref();
        $this->assertInstanceOf("Href", $href);
    }

}
