<?php
/**
 * Created on Mar 29 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for MapUtil.
 */
class MapUtilTest extends \PHPUnit_Framework_TestCase {
    /**
     * @covers MapUtil::get
     */
    public function testGet() {
        $map = array(
           "name" => "user name",
           "age" => 24,
           "city" => array("Montpellier", "Toulouse", 34000, "Paris"),
           "phone" => "047839749347"
        );
        $actual = \MapUtil::get($map, "age");
        $excepted = 24;
        $this->assertEquals($excepted, $actual);
        $actual = \MapUtil::get($map, "city");
        $excepted = array("Montpellier", "Toulouse", 34000, "Paris");
        $this->assertEquals($excepted, $actual);
        $actual = \MapUtil::get($map, "email", "email@fruitionsciences.com");
        $excepted = "email@fruitionsciences.com";
        $this->assertEquals($excepted, $actual);
    }
}
?>
