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
    private $careAboutTotal = true; // boolean

    /**
     * @var SearchInfo
     */
    private $searchInfo;

    public function __construct() {
        $this->recordsPerPage = Config::getInstance()->getInt("tablePaging/rowsPerPage", 10);
        $this->searchInfo = new SearchInfo();
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
        $pagingInfo->limit(1000);
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

    /**
     * Set the given number of records per page, and indicate that we don't care
     * about the total number of records in the result set. (i.e: how many other
     * pages there are). This may slightly improve performance.
     *
     * @param long $recordsPerPage
     */
    public function limit($recordsPerPage) {
        $this->setRecordsPerPage($recordsPerPage);
        $this->setCareAboutTotal(false);
    }

    public function setTotalRows($totalRows) {
        $this->totalRows = $totalRows;
    }

    public function getTotalRows() {
        return $this->totalRows;
    }
    /**
     * @return integer
     */
    public function getTotalPages() {
        return ceil($this->totalRows / $this->recordsPerPage);
    }

    /**
     * @return integer
     */
    public function getFirstRecord() {
        return abs($this->pageNumber * $this->recordsPerPage);
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

    /**
     * @return SearchInfo
     */
    public function getSearchInfo() {
        return $this->searchInfo;
    }

    /**
     * Stores the string to look up for filtering
     * @param String $searchString
     */
    public function setSearchString($searchString) {
        $this->searchInfo->setSearchString($searchString);
    }

    /**
     * Stores the columns on which filtering has to be applied
     * @param String[] $searchColumns
     */
    public function setSearchColumns($searchColumns) {
        $this->searchInfo->setSearchColumns($searchColumns);
    }

    /**
     * If you don't care to know what's the total number of records found, set
     * this to false. It can increase performance slightly.
     *
     * @param boolean $careAboutTotal
     */
    public function setCareAboutTotal($careAboutTotal) {
        $this->careAboutTotal = $careAboutTotal;
    }

    /**
     * @return boolean
     */
    public function isCareAboutTotal() {
        return $this->careAboutTotal;
    }
}
