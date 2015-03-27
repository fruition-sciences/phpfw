<?php
/**
 * created on Apr 22 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
use utils\dataProvider\ConfigProvider;
/**
 * Test class for \PagingInfo.
 */
class PagingInfoTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PagingInfo
     */
    protected $paging;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->paging = new \PagingInfo();
    }

    /**
     * @covers PagingInfo::__construct
     */
    public function test__construct() {
        //40 is define in config.xml file
        $this->assertEquals(40, $this->paging->getRecordsPerPage());
    }

    /**
     * @covers PagingInfo::newSorter
     */
    public function testNewSorter() {
        $orderColumn = 'username';
        $isAscending = false;
        $newPaging = $this->paging->newSorter($orderColumn, $isAscending);
        $this->assertInstanceOf('PagingInfo', $newPaging);
        $this->assertSame($orderColumn, $newPaging->getOrderByColumn());
        $this->assertFalse($newPaging->isOrderByAscending());
    }

    /**
     * @covers PagingInfo::getTotalPages
     */
    public function testGetTotalPages() {
        $recordPerPage = 10;
        $totalRecords = 53;
        $res = 6;
        $this->paging->setRecordsPerPage($recordPerPage);
        $this->paging->setTotalRows($totalRecords);
        $this->assertEquals($res, $this->paging->getTotalPages());
        $recordPerPage = 53;
        $totalRecords = 10;
        $res = 1;
        $this->paging->setRecordsPerPage($recordPerPage);
        $this->paging->setTotalRows($totalRecords);
        $this->assertEquals($res, $this->paging->getTotalPages());

    }

    /**
     * @covers PagingInfo::getFirstRecord
     */
    public function testGetFirstRecord() {
        $pageNumber = 10;
        $recordPerPage = 5;
        $res = 50;
        $this->paging->setPageNumber($pageNumber);
        $this->paging->setRecordsPerPage($recordPerPage);
        $this->assertEquals($res, $this->paging->getFirstRecord());
    }

    /**
     * @covers PagingInfo::isLastPage
     */
    public function testIsLastPage() {
        $pageNumber = 3;
        $recordPerPage = 5;
        $totalRows = 15;
        $this->paging->setPageNumber($pageNumber);
        $this->paging->setRecordsPerPage($recordPerPage);
        $this->paging->setTotalRows($totalRows);
        $this->assertTrue($this->paging->isLastPage());
    }

    /**
     * @covers PagingInfo::setPageNumber
     */
    public function testSetPageNumber() {
        $this->paging = new \PagingInfo();
        $this->assertEquals(0, $this->paging->getPageNumber());
        $this->paging->setPageNumber(20);
        $this->assertSame(20, $this->paging->getPageNumber());
    }

    /**
     * @covers PagingInfo::getPageNumber
     */
    public function testGetPageNumber() {
        $this->assertEquals(0, $this->paging->getPageNumber());
    }

    /**
     * @covers PagingInfo::setRecordsPerPage
     */
    public function testSetRecordsPerPage() {
        $this->paging->setRecordsPerPage(27);
        $this->assertSame(27, $this->paging->getRecordsPerPage());
    }

    /**
     * @covers PagingInfo::getRecordsPerPage
     */
    public function testGetRecordsPerPage() {
        $this->paging->setRecordsPerPage(27);
        $this->assertSame(27, $this->paging->getRecordsPerPage());
    }

    /**
     * @covers PagingInfo::setTotalRows
     */
    public function testSetTotalRows() {
        $this->assertNull($this->paging->getTotalRows());
        $this->paging->setTotalRows(12);
        $this->assertSame(12, $this->paging->getTotalRows());
    }

    /**
     * @covers PagingInfo::getTotalRows
     */
    public function testGetTotalRows() {
        $this->paging->setTotalRows(12);
        $this->assertSame(12, $this->paging->getTotalRows());
    }

    /**
     * @covers PagingInfo::setOrderByColumn
     */
    public function testSetOrderByColumn() {
        $this->assertEmpty($this->paging->getOrderByColumn());
        $this->paging->setOrderByColumn("username");
        $this->assertSame("username", $this->paging->getOrderByColumn());
    }

    /**
     * @covers PagingInfo::getOrderByColumn
     */
    public function testGetOrderByColumn() {
        $this->paging->setOrderByColumn("username");
        $this->assertSame("username", $this->paging->getOrderByColumn());
    }

    /**
     * @covers PagingInfo::setOrderByAscending
     */
    public function testSetOrderByAscending() {
        $this->assertTrue($this->paging->isOrderByAscending());
        $this->paging->setOrderByAscending(false);
        $this->assertFalse($this->paging->isOrderByAscending());
    }

    /**
     * @covers PagingInfo::isOrderByAscending
     */
    public function testIsOrderByAscending() {
        $this->paging->setOrderByAscending(false);
        $this->assertFalse($this->paging->isOrderByAscending());
    }
}
?>
