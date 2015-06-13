<?php
/*
 * Created on May 30, 2015
 * Author: Estelle Wan
 *
 * Purpose: Stores filtering information currently used in datatables.
 * Current usage:
 *     Given a search string and a list of column names,
 *     for e.g.
 *         { searchString: "john", searchColumns: ["name", "age"] },
 *     query all records in which ANY of the given columns matches $searchString
 */

class SearchInfo {
 
    /**
     * @var String
     */
    private $searchString;
    
    /**
     * @var String[]
     */
    private $searchColumns;
    
    /**
     * @return String
     */
    public function getSearchString() {
        return $this->searchString;
    }
    
    /**
     * @param String $searchString
     */
    public function setSearchString($searchString) {
        $this->searchString = $searchString;
    }
    
    /**
     * @return String[]
     */
    public function getSearchColumns() {
        return $this->searchColumns;
    }
    
    /**
     * @param String[] $searchColumns
     */
    public function setSearchColumns($searchColumns) {
        $this->searchColumns = $searchColumns;
    }
}