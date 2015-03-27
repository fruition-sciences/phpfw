<?php
/**
 * Date: 05/07/15
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for Password.
 */
class PasswordTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Password::__construct
     */
    public function test__construct() {
        $pass = new \Password("secret string");
        $this->assertEquals("input", $pass->getType());
        $this->assertEquals("password", $pass->get("type"));
    }
}
