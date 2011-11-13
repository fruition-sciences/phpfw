<?php
/*
 * Created on Nov 2, 2007
 * Author: Yoni Rosenbaum
 */

class PagingInfo {
    private $pageNumber = 0;
    private $recordsPerPage;
    private $totalRows;
    private $orderByColumn;
    private $orderByAscending = true; // boolean

    public function __construct() {
        $this->recordsPerPage = Config::getInstance()->getInt("tablePaging/rowsPerPage", 10);
    }

    /**
     * Creates a new PaginInfo object which sorts by the given column.
     * Records per page is set to a large-enough number.
     * This method can be used when you need a certain finder method to sort
     * by a specific field. 
     * 
     * @param String $orderByColumn
     * @param Boolean $orderByAscending
     * @return PagingInfo
     */
    public static function newSorter($orderByColumn, $orderByAscending=true) {
        $pagingInfo = new PagingInfo();
        $pagingInfo->setRecordsPerPage(1000);
        $pagingInfo->setOrderByColumn($orderByColumn);
        $pagingInfo->setOrderByAscending($orderByAscending);
        return $pagingInfo;
    }

    public function setPageNumber($pageNumber) {
        $this->pageNumber = $pageNumber;
    }

    /**
     * Get the current page number. (0 based)
     */
    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function setRecordsPerPage($recordsPerPage) {
	    $this->recordsPerPage = $recordsPerPage;
	}

    public function getRecordsPerPage() {
        return $this->recordsPerPage;
    }

    public function setTotalRows($totalRows) {
        $this->totalRows = $totalRows;
    }

    public function getTotalRows() {
        return $this->totalRows;
    }

    public function getTotalPages() {
        return ceil($this->totalRows / $this->recordsPerPage);
    }

    public function getFirstRecord() {
        return $this->pageNumber * $this->recordsPerPage;
    }

    public function setOrderByColumn($orderByColumn) {
        $this->orderByColumn = $orderByColumn;
    }

    public function getOrderByColumn() {
        return $this->orderByColumn;
    }

    public function setOrderByAscending($orderByAscending) {
        $this->orderByAscending = $orderByAscending;
    }

    public function isOrderByAscending() {
        return $this->orderByAscending;
    }

    public function isLastPage() {
        return ($this->pageNumber+1) * $this->recordsPerPage >= $this->totalRows;
    }
}
