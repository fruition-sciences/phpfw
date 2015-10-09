<?php
/**
 * created on Apr 22 2015
 * @author Sidiki Coulibaly
 */

namespace tests\units;
/**
 * Test class for QueryPager.
 */
class QueryPagerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PagingInfo
     */
    protected $paging;

    protected function setUp() {
        $this->paging = new \PagingInfo();
        $this->paging->setRecordsPerPage(5);
    }

    /**
     * @covers QueryPager::getQuery
     * @covers QueryPager::__construct
     */
    public function testGetQuery() {
        $sql = "SELECT * FROM office";
        $expected = "select SQL_CALC_FOUND_ROWS * from (SELECT * FROM office) _x limit 0, 5";
        $pager = new \QueryPager($sql, $this->paging);
        $this->assertEquals($expected, $pager->getQuery());
    }

    /**
     * @covers QueryPager::getQuery
     * @covers QueryPager::__construct
     */
    public function testGetQueryWithOrderBy() {
        $sql = "SELECT * FROM office";
        $this->paging->setOrderByColumn('officeName');
        $expected = "select SQL_CALC_FOUND_ROWS * from (SELECT * FROM office) _x order by officeName asc limit 0, 5";
        $pager = new \QueryPager($sql, $this->paging);
        $this->assertEquals($expected, $pager->getQuery());
    }

    /**
     * @covers QueryPager::getQuery
     * @covers QueryPager::__construct
     */
    public function testGetQueryWithFilter() {
        $sql = "SELECT * FROM office";
        $this->paging->setOrderByColumn('officeName');
        $this->paging->setOrderByAscending(false);
        $expected = "select SQL_CALC_FOUND_ROWS * from (SELECT * FROM office) _x order by officeName desc limit 0, 5";
        $pager = new \QueryPager($sql, $this->paging);
        $this->assertEquals($expected, $pager->getQuery());
    }

    /**
     * @covers QueryPager::getQuery
     * @covers QueryPager::__construct
     */
    public function testGetQueryWithNullPagingInfo() {
        $sql = "SELECT * FROM office";
        $expected = "SELECT * FROM office";
        $pager = new \QueryPager($sql, null);
        $this->assertEquals($expected, $pager->getQuery());
    }
}
?>
