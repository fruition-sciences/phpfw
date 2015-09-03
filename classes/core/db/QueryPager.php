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
    
    /**
     * Some old queries do not support prepared statements. 
     * In this case, we need to disable the filtering functionality.
     * 
     * @var Boolean
     */
    private $includeFilter;

    /**
     * @param String $sql the sql query for which paging and filtering needs to be applied
     * @param PagingInfo $paging contains information about paging state
     * @param Boolean $includeFilter indicates whether we want to explicitly apply filtering or not
     */
    public function __construct($sql, $paging, $includeFilter=true) {
        $this->sql = $sql;
        $this->paging = $paging;
        $this->includeFilter = $includeFilter;
    }

    /**
     * Return the query, with the paging applied to it.
     * @return String the sql query
     */
    public function getQuery() {
        if ($this->paging == null) {
            return $this->sql;
        }
        
        $search = $this->paging->getSearchInfo();
        
        if ($search != null && $this->includeFilter) {
            $this->applySearchFilter($search);
        }
        
        $newSql = "select SQL_CALC_FOUND_ROWS * from (" . $this->sql . ") _x";
        if ($this->paging->getOrderByColumn()) {
            $newSql .= " order by " . $this->paging->getOrderByColumn();
            $newSql .= $this->paging->isOrderByAscending() ? ' asc' : ' desc';
        }
        $newSql .= " limit " . $this->paging->getFirstRecord() . ", " . $this->paging->getRecordsPerPage();
        return $newSql;
    }
    
    /**
     * Applies additional filters to this query based on the given SearchInfo.
     * 
     * The SearchInfo defines a search string and a list of columns. 
     * This translates into an additional 'where' clause, in which we filter for 
     * all records which match the search string on ANY of those columns.
     * 
     * For example, given SearchInfo: 
     *     {
     *         searchString: "john", 
     *         searchColumns: ["name", "age"]
     *     }
     * The following filter clause will be added:
     *     sqlBuilder.filter('name like ? or age like ?', 'ss', 'john', 'john');
     *
     * @see SQLBuilder::filter which construct adds a 'where' condition
     * @param SearchInfo $search contains the filtering info
     */
    private function applySearchFilter($search) {
     
        $searchString = $search->getSearchString();
        $searchColumns = $search->getSearchColumns();
        $numColumns = count($searchColumns);
         
        if (empty($searchString) || $numColumns < 1) return;
         
        if ($this->sql instanceof SQLBuilder) {
            /* 
             * Given a list of column names and search string, populate
             * 1. $condition - SQL clause
             * 2. $vartypes - types of parameters and
             * 3. $args - list of search strings
             * to be used for prepared statements.
             */
            $conditions = array();
            foreach ($searchColumns as $column) {
                $conditions[] = $column . " like ?";
            }
             
            $varTypes = str_repeat('s', $numColumns); // types of parameters passed
            $args = array_fill(0, $numColumns, "%$searchString%"); // list of search strings repeated up to number of columns
             
            call_user_func_array(array($this->sql, "filter"), array_merge(array(implode(" or ", $conditions), $varTypes), $args));
           
        } else {
            throw IllegalStateException("Cannot support sql objects other than SQLBuilder.");
        }
    }
}