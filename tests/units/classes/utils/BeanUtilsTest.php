<?php
/**
 * Created on Apr 29 2015
 * @author Sidiki Coulibaly
 */
namespace tests\units;
/**
 * Test class for BeanUtils.
 */
class BeanUtilsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers BeanUtils::getBeanIds
     */
    public function testGetBeanIds() {
        $beans = $this->generateBeans(4);
        $actual = \BeanUtils::getBeanIds($beans);
        $expected = array(0, 1, 2, 3);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers BeanUtils::getValues
     */
    public function testGetValues() {
        $beans = $this->generateBeans(4);
        $actual = \BeanUtils::getValues($beans, "getId");
        $expected = array(0, 1, 2, 3);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param $count
     * @return array : contents generated beans
     */
    protected function generateBeans($count) {
        $beans = array();
        for ($i = 0; $i < $count; $i++) {
            $myBean = new \BlockKcBean();
            $myBean->setId($i);
            $beans[] = $myBean;
        }
        return $beans;
    }
}
?>
