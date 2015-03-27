<?php
/**
 *Created on Mar 17 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;

/**
 * Test class for ErrorManager.
 */
class ErrorManagerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ErrorManager
     */
    protected $manager;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->manager = new \ErrorManager();
    }

    /**
     * @covers ErrorManager::addError
     */
    public function testAddError() {
        $this->assertEmpty($this->manager->getErrors());
        $this->manager->addError('fail');
        $this->assertContainsOnlyInstancesOf('\UserError', $this->manager->getErrors());
        return $this->manager;
    }

    /**
     * @covers ErrorManager::addErrorMessage
     */
    public function testAddErrorMessage() {
        $this->assertEmpty($this->manager->getErrors());
        $this->manager->addErrorMessage('The required field is empty');
        $this->assertContainsOnlyInstancesOf('\UserError', $this->manager->getErrors());
    }

    /**
     * @covers ErrorManager::getErrors
     * @depends testAddError
     */
    public function testGetErrors(\ErrorManager $err) {
        $this->assertNotEmpty($err->getErrors());
    }

    /**
     * @covers ErrorManager::setErrorMessage
     * @depends testAddError
     * @todo test passed but it is not pertinent. this function should return somthing
     * or throw an exception when tag doesn't exist.
     */
    public function testSetErrorMessage(\ErrorManager $err) {
        $before =  $err->getErrors();
        $this->assertNotEmpty($before);
        $err->setErrorMessage('wrong', 'The required field is empty');
        $this->assertSame($before, $err->getErrors());
        $err->setErrorMessage('fail', 'The required field is empty');
        $this->assertContainsOnlyInstancesOf('\UserError', $this->manager->getErrors());
        $this->assertEquals('The required field is empty', $err->getErrors()[0]->__toString());
    }

    /**
     * @covers ErrorManager::hasErrors
     */
    public function testHasErrors() {
        $this->manager = new \ErrorManager();
        $this->assertFalse($this->manager->hasErrors());
        $this->manager->addError('error');
        $this->assertTrue($this->manager->hasErrors());
    }
}
?>
