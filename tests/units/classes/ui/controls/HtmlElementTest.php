<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for HtmlElement.
 */
class HtmlElementTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \HtmlElement
     */
    protected $element;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->element = new \HtmlElement("input", "sensorID");
    }

    /**
     * @covers HtmlElement::__construct
     */
    public function test__construct() {
        $elt = new \HtmlElement("input", "sensorID");
        $this->assertEquals("input", $elt->getType());
        $this->assertEquals("sensorID", $elt->getName());
    }

    /**
     * @covers HtmlElement::__construct
     */
    public function test__constructEmptyName() {
        $elt = new \HtmlElement("input");
        $this->assertEquals("input", $elt->getType());
        $this->assertEmpty($elt->getName());
    }

    /**
     * @covers HtmlElement::setBody
     */
    public function testSetBody() {
        $body = "html body";
        $this->assertNull($this->element->getBody());
        $elt = $this->element->setBody($body);
        $actual = $elt->getBody();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($body, $actual);
    }

    /**
     * @covers HtmlElement::getBody
     */
    public function testGetBody() {
        $body = "html body";
        $elt = $this->element->setBody($body);
        $actual = $elt->getBody();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($body, $actual);
    }

    /**
     * @covers HtmlElement::setValue
     */
    public function testSetValue() {
        $value = "html value";
        $this->assertNull($this->element->getValue());
        $elt = $this->element->setValue($value);
        $actual = $elt->getValue();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($value, $actual);
    }

    /**
     * @covers HtmlElement::getValue
     */
    public function testGetValue() {
        $value = "html value";
        $elt = $this->element->setValue($value);
        $actual = $elt->getValue();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($value, $actual);
    }

    /**
     * @covers HtmlElement::__toString
     */
    public function test__toString() {
        $actual = $this->element->__toString();
        $expected = '<input name="sensorID"></input>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::getElementOpenTag
     * @covers HtmlElement::getAttributesAsString
     * @covers HtmlElement::getCssClassNamesAsString
     */
    public function testGetElementOpenTag() {
        $actual = $this->element->getElementOpenTag();
        $expected = '<input name="sensorID">';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::getElementOpenTag
     * @covers HtmlElement::getAttributesAsString
     * @covers HtmlElement::getCssClassNamesAsString
     */
    public function testGetElementOpenTagWithCss() {
        $this->element = $this->element->addClass("col-lg-12");
        $this->element = $this->element->set("size", "12px");
        $actual = $this->element->getElementOpenTag();
        $expected = '<input name="sensorID" size="12px" class="col-lg-12">';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::getElementCloseTag
     */
    public function testGetElementCloseTag() {
        $actual = $this->element->getElementCloseTag();
        $expected = '</input>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::getName
     */
    public function testGetName() {
        $actual = $this->element->getName();
        $expected = 'sensorID';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::setType
     */
    public function testSetType() {
        $this->assertEquals("input", $this->element->getType());
        $elt = $this->element->setType("select");
        $actual = $elt->getType();
        $expected = 'select';
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::getType
     */
    public function testGetType() {
        $actual = $this->element->getType();
        $expected = 'input';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::confirm
     */
    public function testConfirm() {
        $elt = $this->element->confirm("Accept terms");
        $actual = $elt->get("onclick");
        $expected = "if (!confirm('Accept terms')) return false";
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::setTooltip
     */
    public function testSetTooltip() {
        $elt = $this->element->setTooltip("Validate changes");
        $actual = $elt->get("title");
        $expected = "Validate changes";
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::setClass
     */
    public function testSetClass() {
        $elt = $this->element->setClass("color");
        $actual = $elt->getCssClasses();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertContains("color", $actual);
    }

    /**
     * @covers HtmlElement::setClass
     */
    public function testSetClassReplace() {
        $elt = $this->element->setClass("color");
        $elt = $elt->setClass("width");
        $actual = $elt->getCssClasses();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertContains("width", $actual);
    }

    /**
     * @covers HtmlElement::getCssClasses
     */
    public function testGetClasses() {
        $elt = $this->element->setClass("width");
        $actual = $elt->getCssClasses();
        $this->assertContains("width", $actual);
    }

    /**
     * @covers HtmlElement::addClass
     */
    public function testAddClass() {
        $elt = $this->element->addClass("color");
        $actual = $elt->getCssClasses();
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertTrue($actual["color"]);
    }

    /**
     * @covers HtmlElement::set
     */
    public function testSet() {
        $elt = $this->element->set("color", "red");
        $actual = $elt->get("color");
        $expected = "red";
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers HtmlElement::set
     */
    public function testSetCss() {
        $elt = $this->element->set("class", "col-lg-12");
        $actual = $elt->getCssClasses();
        $expected = "col-lg-12";
        $this->assertInstanceOf("HtmlElement", $elt);
        $this->assertContains($expected, $actual);
    }
}
