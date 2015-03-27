<?php
/**
 * Created on May 07 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for ConstraintFactory.
 */
class ConstraintFactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ConstraintFactory::newConstraint
     */
    public function testNewConstraint() {
        $type = \ConstraintFactory::REQUIRED;
        $actual = \ConstraintFactory::newConstraint("required", $type);
        $this->assertInstanceOf("RequiredConstraint", $actual);
        $this->assertEquals($type, $actual->getType());
    }

    /**
     * @covers ConstraintFactory::newConstraint
     */
    public function testNewConstraintDate() {
        $type = \ConstraintFactory::DATE;
        $actual = \ConstraintFactory::newConstraint("required", $type);
        $this->assertInstanceOf("DateConstraint", $actual);
        $this->assertEquals($type, $actual->getType());
    }

    /**
     * @covers ConstraintFactory::newConstraint
     */
    public function testNewConstraintNumber() {
        $type = \ConstraintFactory::NUMBER;
        $actual = \ConstraintFactory::newConstraint("required", $type);
        $this->assertInstanceOf("NumberConstraint", $actual);
        $this->assertEquals($type, $actual->getType());
    }

    /**
     * @covers ConstraintFactory::newConstraint
     * @expectedException Exception
     * @expectedExceptionMessage Unknown constraint type: allowed
     */
    public function testNewConstraintException() {
        $type = "allowed";
        $actual = \ConstraintFactory::newConstraint("required", $type);
        $this->assertNull($actual);
    }
}
