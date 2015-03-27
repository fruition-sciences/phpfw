<?php
/**
 * Created on Apr 29 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for BeanMap.
 */
class BeanMapTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \BeanMap
     */
    protected $beanMap;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->beanMap = new \BeanMap();
    }

    /**
     * @covers BeanMap::set
     * @covers BeanMap::get
     * @covers BeanMap::getKey
     */
    public function testSet() {
        $bean = new \BlockKcBean();
        $before = $this->beanMap->get($bean);
        $this->beanMap->set($bean);
        $actual = $this->beanMap->get($bean);
        $this->assertNull($before);
        $this->assertEquals($bean, $actual);
    }

    /**
     * @covers BeanMap::setAll
     * @covers BeanMap::getAll
     */
    public function testSetAll() {;
        $beanArray = $this->generateBeans(3);
        $this->beanMap->setAll($beanArray, true);
        $actual = $this->beanMap->getAll();
        $this->assertEquals($beanArray, $actual);
    }

    /**
     * @covers BeanMap::getById
     * @covers BeanMap::__construct
     */
    public function testGetById() {
        $beanArray = $this->generateBeans(3);
        $this->beanMap = new \BeanMap($beanArray);
        $bean = new \BlockKcBean();
        $this->beanMap->set($bean);
        $actual = $this->beanMap->getById(-1);
        $notExist = $this->beanMap->getById(5);
        $this->assertNull($notExist);
        $this->assertEquals($bean, $actual);
    }

    /**
     * @covers BeanMap::getAllAsList
     */
    public function testGetAllAsList() {
        $beanArray = $this->generateBeans(5);
        $this->beanMap->setAll($beanArray);
        $actual = $this->beanMap->getAllAsList();
        $this->assertContainsOnlyInstancesOf("BlockKcBean", $actual);
        $this->assertEquals($beanArray, $actual);
    }

    /**
     * @covers BeanMap::getIds
     */
    public function testGetIds() {
        $beanArray = $this->generateBeans(3);
        $this->beanMap->setAll($beanArray);
        $actual = $this->beanMap->getIds();
        $expected = array(0, 1, 2);
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
