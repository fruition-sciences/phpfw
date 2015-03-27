<?php
/**
 * created on Apr 21 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Element.
 */
class ElementTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Element
     */
    protected $elt;

    /**.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->elt = new \Element();
    }

    /**
     * @covers Element::set
     * @todo Possible to set empty key OK ?.
     */
    public function testSet() {
        $keyOne   = "firstkey";
        $valueOne = "firstValue";

        $this->assertEmpty($this->elt->getAll());
        $result = $this->elt->set($keyOne, $valueOne);
        $this->assertInstanceOf('Element', $result);
        $this->assertSame($valueOne, $result->get($keyOne));
        $result = $result->set($keyOne);
        $this->assertEmpty($result->get($keyOne));
        $result = $result->set('');  // wrong no ?
        $this->assertNull($result->get(''));
    }

    /**
     * @covers Element::get
     * @todo Implement testGet().
     */
    public function testGet() {
        $key   = "class";
        $value = "col-xs-12";

        $this->assertNull($this->elt->get("anyKey"));
        $result = $this->elt->set($key, $value);
        $this->assertInstanceOf('Element', $result);
        $this->assertSame($value, $result->get($key));
    }

    /**
     * @covers Element::un_set
     */
    public function testUn_set() {
        $key   = "src";
        $value = "img/logo.pgn";

        $result = $this->elt->set($key, $value);
        $this->assertSame($value, $result->get($key));
        $result->un_set($key);
        $this->assertNull($result->get($key));
    }

    /**
     * @covers Element::removeAll
     */
    public function testRemoveAll() {
        $keyOne   = "alt";
        $valueOne = "logo fruition";
        $keyTwo   = "target";
        $valueTwo = "_blank";
        $keyThree   = "href";
        $valueThree = "fruitionsciences.com";
        $atts = array(
            $keyOne =>   $valueOne,
            $keyTwo => $valueTwo,
            $keyThree => $valueThree
        );

        $this->elt->setAll($atts);
        $this->assertNotEmpty($this->elt->getAll());
        $this->elt->removeAll();
        $this->assertEmpty($this->elt->getAll());
    }

    /**
     * @covers Element::getAll
     * @todo Implement testGetAll().
     */
    public function testGetAll() {
        $keyOne   = "alt";
        $valueOne = "logo fruition";
        $keyTwo   = "target";
        $valueTwo = "_blank";
        $keyThree   = "href";
        $valueThree = "fruitionsciences.com";
        $atts = array(
            $keyOne =>   $valueOne,
            $keyTwo => $valueTwo,
            $keyThree => $valueThree
        );

        $this->assertEmpty($this->elt->getAll());
        $this->elt->setAll($atts);
        $this->assertContains($valueOne, $this->elt->getAll());
        $this->assertContains($valueTwo, $this->elt->getAll());
        $this->assertContains($valueThree, $this->elt->getAll());
    }

    /**
     * @covers Element::setAll
     */
    public function testSetAll() {
        $keyOne   = "alt";
        $valueOne = "logo fruition";
        $keyTwo   = "target";
        $valueTwo = "_blank";
        $keyThree   = "href";
        $valueThree = "fruitionsciences.com";
        $atts = array(
            $keyOne =>   $valueOne,
            $keyTwo => $valueTwo,
            $keyThree => $valueThree
        );

        $this->assertEmpty($this->elt->getAll());
        $this->elt->setAll($atts);
        $this->assertSame($valueOne, $this->elt->get($keyOne));
        $this->assertSame($valueTwo, $this->elt->get($keyTwo));
        $this->assertSame($valueThree, $this->elt->get($keyThree));
    }
}
?>
