<?php
/**
 * Date: 05/005/15
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Button.
 */
class ButtonTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Button
     */
    protected $button;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->button = new \Button("button");
    }

    /**
     * @covers Button::__construct
     */
    public function test__construct() {
        $this->button = new \Button("Office Name");
        $this->assertSame("button", $this->button->getType());
    }

    /**
     * @covers Button::setUrl
     */
    public function testSetUrl() {
        $url = new \Href("vmms/vineyar?id=78");
        $resultButton = $this->button->setUrl($url);
        $actual = $resultButton->getHref();
        $this->assertSame($url, $actual);
    }

    /**
     * @covers Button::setUrl
     */
    public function testSetUrlText() {
        $url = "vmms/vineyar?id=78";
        $resultButton = $this->button->setUrl($url);
        $actual = $resultButton->getHref();
        $expected = new \Href($url);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Button::setAction
     */
    public function testSetAction() {
        $this->button = new \Button("button");
        $action = "delete";
        $resultButton = $this->button->setAction($action);
        $actual = $resultButton->getHref()->get("_ac");
        $this->assertEquals($action, $actual);
    }

    /**
     * @covers Button::noSubmit
     */
    public function testNoSubmit() {
        $resultButton = $this->button->noSubmit();
        $this->assertFalse($resultButton->isSubmit());
    }

    /**
     * @covers Button::set
     */
    public function testSet() {
        $this->button = new \Button("button");
        $key = "style";
        $value = "color:red";
        $resultButton = $this->button->set($key, $value);
        $actual = $resultButton->get($key);
        $this->assertEquals($value, $actual);
    }

    /**
     * @covers Button::set
     */
    public function testSetClickKey() {
        $key = "onclick";
        $value = "send";
        $resultButton = $this->button->set($key, $value);
        $actual = $resultButton->getOnclick();
        $this->assertContains($value, $actual);
    }

    /**
     * @covers Button::setParam
     */
    public function testSetParam() {
        $key = "id";
        $value = "124";
        $resultButton = $this->button->setParam($key, $value);
        $actual = $resultButton->getHref()->get($key);
        $this->assertEquals($value, $actual);
    }

    /**
     * @covers Button::unsetParam
     */
    public function testUnsetParam() {
        $key = "id";
        $value = "124";
        $resultButton = $this->button->setParam($key, $value);
        $actual = $resultButton->getHref()->get($key);
        $this->assertEquals($value, $actual);
        $resultButton = $this->button->unsetParam($key);
        $actual = $resultButton->getHref()->get($key);
        $this->assertNull($actual);
    }

    /**
     * @covers Button::__toString
     * @covers Button::setOnClick
     */
    public function test__toString() {
        $resultButton = $this->button->setUrl("vmms/account");
        $resultButton = $resultButton->setTarget("_blank");
        $actual = $resultButton->__toString();
        $expected = '<button onclick="button_submit(\'vmms/account\', \'_blank\'); return false; ">button</button>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Button::__toString
     * @covers Button::setOnClick
     */
    public function test__toStringType() {
        $resultButton = $this->button->setUrl("vmms/account");
        $resultButton->setType("notButton");
        $actual = $resultButton->__toString();
        $expected = '<notButton onclick="button_submit(\'vmms/account\'); return false; " button="1">button</notButton>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Button::setTitle
     */
    public function testSetTitle() {
        $title = "Button title";
        $this->button->setTitle($title);
        $actual = $this->button->getTitle();
        $this->assertEquals($title, $actual);
    }

    /**
     * @covers Button::getTitle
     */
    public function testGetTitle() {
        $title = "Button title";
        $this->button = new \Button($title);
        $actual = $this->button->getTitle();
        $this->assertEquals($title, $actual);
    }

    /**
     * @covers Button::setTarget
     */
    public function testSetTarget() {
        $target = "_blank";
        $this->assertNull($this->button->getTarget());
        $resultButton = $this->button->setTarget($target);
        $actual = $resultButton->getTarget();
        $this->assertEquals($target, $actual);
    }

    /**
     * @covers Button::getTarget
     */
    public function testGetTarget() {
        $target = "_blank";
        $resultButton = $this->button->setTarget($target);
        $actual = $resultButton->getTarget();
        $this->assertEquals($target, $actual);
    }

    /**
     * @covers Button::getHref
     */
    public function testGetHref() {
        $this->assertInstanceOf("Href", $this->button->getHref());
    }

    /**
     * @covers Button::isSubmit
     */
    public function testIsSubmit() {
        $this->assertTrue($this->button->isSubmit());
    }

    /**
     * @covers Button::getOnclick
     */
    public function testGetOnclick() {
        $this->assertEmpty($this->button->getOnclick());
    }
}
