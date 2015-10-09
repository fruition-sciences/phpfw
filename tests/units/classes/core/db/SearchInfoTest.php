<?php
/**
 * created on October 9 2015
 * @author Valentine Berge
 */

namespace tests\units;
/**
 * Test class for SearchInfo.
 */
class SearchInfoTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \String
     */
    protected $searchString;
    
    /**
     * @var \String[]
     */
    protected $searchColumns;

    /**
     * @covers SearchInfo::getSearchString
     */
    public function testGetSearchString() {
        $searchInfo = new \SearchInfo();
        $result = $searchInfo->setSearchString('string');
        $this->assertEquals('string', $searchInfo->getSearchString());
    }
    
    /**
     * @covers SearchInfo::setSearchString
     */
    public function testSetSearchString() {
        $searchInfo = new \SearchInfo();
        $this->assertNull($searchInfo->getSearchString());
        $result = $searchInfo->setSearchString('string');
        $this->assertEquals('string', $searchInfo->getSearchString());
    }
    
    /**
     * @covers SearchInfo::getSearchColumns
     */
    public function testGetSearchColumns() {
        $searchInfo = new \SearchInfo();
        $searchColumns = array('string1', 'string2');
        $result = $searchInfo->setSearchColumns($searchColumns);
        $this->assertEquals($searchColumns, $searchInfo->getSearchColumns());
    }
    
    /**
     * @covers SearchInfo::setSearchColumns
     */
    public function testSetSearchColumns() {
        $searchInfo = new \SearchInfo();
        $this->assertNull($searchInfo->getSearchColumns());
        $searchColumns = array('string1', 'string2');
        $result = $searchInfo->setSearchColumns($searchColumns);
        $this->assertEquals($searchColumns, $searchInfo->getSearchColumns());
    }
}
?>
