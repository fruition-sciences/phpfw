<?php
/**
 * Date: 16/04/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Href.
 */
class HrefTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Href
     */
    private $href;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->href = new \Href('vmms/vineyard');
    }

    /**
     * @covers Href::current
     */
    public function testCurrent() {
        $this->href = \Href::current();
        $this->assertNotEmpty($this->href->__toString());
    }

    /**
     * @covers Href::from_url
     * @covers Href::setQuery
     */
    public function testFrom_url() {
        $_SERVER['QUERY_STRING'] = 'test';
        $url = "http://localhost/vmms/admin/vineyard?id=78&size=10";
        $this->href = \Href::from_url($url);
        $actual = $this->href->__toString();
        $expected = "http://localhost/vmms/admin/vineyard";
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers Href::from_url
     */
    public function testFrom_urlNoQueryString() {
        $url = "http://localhost/vmms/admin/vineyard";
        $this->href = \Href::from_url($url);
        $actual = $this->href->__toString();
        $this->assertSame($url, $actual);
        $this->assertEmpty($this->href->getQueryString());
    }

    /**
     * @covers Href::__toString
     */
    public function test__toString() {
        $url = "http://www.fruitionsciences.com/admin/vineyard?id=169";
        $enchor = "tips";
        $newHref = \Href::from_url($url);
        $newHref->setAnchor($enchor);
        $actual = $newHref->__toString();
        $expected = $url. "#".$enchor;
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers Href::getQueryString
     * @covers Href::getQueryStringNameValue
     */
    public function testGetQueryString() {
        $url = "http://www.fruitionsciences.com/admin/vineyard?id=169&size=10";
        $newHref = \Href::from_url($url);
        $actual = $newHref->getQueryString();
        $expected = "id=169&size=10";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Href::getQueryString
     */
    public function testGetQueryStringEmpty() {
        $url = "http://www.fruitionsciences.com/admin/vineyard";
        $newHref = \Href::from_url($url);
        $actual = $newHref->getQueryString();
        $this->assertEmpty($actual);
    }

    /**
     * @covers Href::getQueryString
     */
    public function testGetQueryStringArray() {
        $url = "http://www.fruitionsciences.com/admin/vineyard";
        $newHref = \Href::from_url($url);
        $newHref->setAll(array("id"=>array(1,2), "name"=>"vincent"));
        $actual = $newHref->getQueryString();
        $expected = "id[]=1&id[]=2&name=vincent";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Href::setAnchor
     */
    public function testSetAnchor() {
        $this->assertEquals('vmms/vineyard', $this->href->__toString());
        $this->href->setAnchor("useful");
        $this->assertEquals('vmms/vineyard#useful', $this->href->__toString());
    }

    /**
     * @covers Href::set
     */
    public function testSet() {
        $newHref = $this->href->set("sensor_id", "21");
        $this->assertArrayHasKey("sensor_id", $newHref->getAttributes());
    }

    /**
     * @covers Href::set
     */
    public function testSetobject() {
        $newHref = $this->href->set("link", new \Href("vmms-mobile"));
        $this->assertArrayHasKey("link", $newHref->getAttributes());
        $this->assertContains("vmms-mobile", $newHref->getAttributes());
    }

    /**
     * @covers Href::set
     */
    public function testSetSecondId() {
        $newHref = $this->href->set("id", 11, true);
        $newHref = $newHref->set("id", 121, true);
        $this->assertArrayHasKey("id", $newHref->getAttributes());
        $this->assertContains(array(11, 121), $newHref->getAttributes());
    }

    /**
     * @covers Href::getAttributes
     * @covers Href::__construct
     * @covers Href::parse
     */
    public function testGetAttributes() {
        $url = "http://www.fruitionsciences.com/admin/vineyard?id=169";
        $newHref = new \Href($url);
        $this->assertArrayHasKey("id", $newHref->getAttributes());
        $this->assertContains("169", $newHref->getAttributes());
    }

    /**
     * @covers Href::parse
     */
    public function testParse() {
        $this->assertSame("vmms/vineyard", $this->href->__toString());
    }
}
