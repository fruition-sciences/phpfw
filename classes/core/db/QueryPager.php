<?php
/*
 * Created on Nov 2, 2007
 * Author: Yoni Rosenbaum
 * 
 * The QueryPager applies a PagingInfo object onto a SQL query, allowing the
 * result to be paged.
 */
class QueryPager {
    /**
     * @var String
     */
    private $sql;
    /**
     * @var PagingInfo
     */
    private $paging;

    public function __construct($sql, $paging) {
        $this->sql = $sql;
        $this->paging = $paging;
    }

    /**
     * Return the query, with the paging applied to it.
     * @return String the sql query
     */
    public function getQuery() {
        if ($this->paging == null) {
            return $this->sql;
        }
        $newSql = "select SQL_CALC_FOUND_ROWS * from (" . $this->sql . ") _x";
        if ($this->paging->getOrderByColumn()) {
            $newSql .= " order by " . $this->paging->getOrderByColumn();
            $newSql .= $this->paging->isOrderByAscending() ? ' asc' : ' desc';
        }
        $newSql .= " limit " . $this->paging->getFirstRecord() . ", " . $this->paging->getRecordsPerPage();
        return $newSql;
    }
}