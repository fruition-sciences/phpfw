<?php
/**
 * Created on Mar 16 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for Bread_Crumbs.
 */
class Bread_CrumbsTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Bread_Crumbs
     */
    protected $crumb;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->crumb = new \Bread_Crumbs();
    }

    /**
     * @covers Bread_Crumbs::add
     */
    public function testAdd() {
        $this->assertEmpty($this->crumb->getAll());
        $this->crumb->add('First');
        $comp = new \Textbox('Second');
        $this->crumb->add($comp);
        $this->assertContains('First', $this->crumb->getAll());
        $this->assertContains($comp, $this->crumb->getAll());
        return $this->crumb;
    }

    /**
     * @covers Bread_Crumbs::__toString
     * @depends testAdd
     */
    public function test__toString(\Bread_Crumbs $elt) {
        $str = "<div class='crumbs'>First &rarr; <input name=\"Second\" type=\"text\"></input></div>";
        $this->assertSame($str, $elt->__toString());
    }

    /**
     * @covers Bread_Crumbs::getAll
     * @depends testAdd
     */
    public function testGetAll(\Bread_Crumbs $elt) {
        $this->assertContains('First', $elt->getAll());

    }

    /**
     * @covers Bread_Crumbs::addAll
     * @todo Implement testAddAll().
     */
    public function testAddAll() {
        $comp = new \Textbox('Second');
        $this->assertEmpty($this->crumb->getAll());
        $this->crumb->addAll(array('First', $comp, 'Third'));
        $this->assertContains('First', $this->crumb->getAll());
        $this->assertContains($comp, $this->crumb->getAll());
    }
}
?>
