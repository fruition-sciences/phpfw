<?php
/*
 * Created on Jul 7, 2007
 * Author: Yoni Rosenbaum
 *
 */

class SQLBuilder {
    private $tables = array();
    private $columns = array();
    private $conditions = array();
    private $order = "";
    private $group = "";
    private $predicate;
    private $limit;

    /**
     * Add a select for the given column and apply the given functions on the column
     * @param String $tableName
     * @param String $alias prefix to be used for all column aliases.
     * @param Array $columns
     * @param Array $functions (optional) Array of the same size as $columns which
     *        contains SQL functions (such as max, avg, min) to apply on the
     *        corresponding column. A null entry in the array means no function
     *        should be apply on corresponding column.
     */
    public function select($tableName, $alias, $columns, $functions=null) {
        $this->from($tableName, $alias);
        $this->addColumns($alias, $columns, $functions);
    }
    
    /** 
     * Select all columns from the table represented by the given bean.
     * Uses the $functions variable of the bean so that specific SQL functions
     * are being used if necessary.
     * 
     * @param String $beanClassName
     * @param String $alias
     */
    public function selectAll($beanClassName, $alias) {
        $this->select($beanClassName::TABLE_NAME, $alias, $beanClassName::$ALL, $beanClassName::$functions);
    }

    /**
     * Add the given columns into the columns array. The columns array indicates
     * which columns will be selected in the query.
     * Optionally, applies the given functions to the columns.
     * 
     * @param $alias the table's alias.
     * @param $columns
     * @param $functions (option) array of the same size of the given columns array.
     * @return unknown_type
     */
    private function addColumns($alias, $columns, $functions=null) {
        $useFunction = is_array($functions) && count($columns) == count($functions);
        foreach ($columns as $k=>$column) {
            if ($useFunction && !empty($functions[$k])){
                $this->columns[] = "{$functions[$k]}(${alias}.${column}) {$functions[$k]}_${alias}_${column}";
            }
            else {
                $this->columns[] = "${alias}.${column} ${alias}_${column}";
            }
        }
    }

    /**
     * Add a custom column.
     *
     * @param String $column column name or sub query.
     * @param String $alias the alias for this column.
     */
    public function column($column, $alias) {
        $this->columns[] = "(${column}) ${alias}";
    }

    public function from($tableName, $alias) {
        $this->tables[$alias] = new SQLJoin($tableName, $alias);
    }

    public function filter($condition) {
        $this->conditions[] = "($condition)";
    }

    /**
     * Use this method in order to perform an explicit regular (inner) join.
     *  
     * @param $tableName
     * @param $alias
     * @param $condition
     * @param $columns (optional) columns to be selected
     */
    public function join($tableName, $alias, $condition, $columns=null) {
        $this->explicitJoin($tableName, $alias, $condition, SQLJoin::INNER_JOIN, $columns);
    }

    /**
     * Performs a left join.
     * 
     * @param $tableName
     * @param $alias
     * @param $condition
     * @param $columns (optional) columns to be selected
     */
    public function leftJoin($tableName, $alias, $condition, $columns=null) {
        $this->explicitJoin($tableName, $alias, $condition, SQLJoin::LEFT_JOIN, $columns);
    }

    /**
     * Performs an expilicit join.
     * 
     * @param $tableName
     * @param $alias
     * @param $condition
     * @param $joinType int self::INNER_JOIN or self::LEFT_JOIN
     * @param $columns (optional) columns to be selected
     */
    public function explicitJoin($tableName, $alias, $condition, $joinType, $columns=null) {
        $this->tables[$alias] = new SQLJoin($tableName, $alias, $condition, $joinType);
        if ($columns) {
            $this->addColumns($alias, $columns);
        }
    }

    public function orderBy($order) {
        $this->order = $order;
    }

    public function groupBy($group) {
        $this->group = $group;
    }

    public function getColumnsString() {
        return arrayToString($this->columns, ",");
    }

    public function __toString()
    {
        $sql = "select ";
        if ($this->predicate) {
            $sql .= $this->predicate . " ";
        }
        $sql .= $this->getColumnsString();
        $sql .= " from " . $this->tablesToString();
        if (count($this->conditions) > 0) {
            $sql .= " where " . arrayToString($this->conditions, " and ");
        }
        if ($this->group) {
            $sql .= " group by " . $this->group;
        }
        if ($this->order) {
            $sql .= " order by " . $this->order;
        }
        if ($this->limit) {
            $sql .= " limit " . $this->limit;
        }
        return $sql;
    }

    /**
     * Set a predicate to be used right after the 'select' statement.
     *
     * @param String $predicate the predicate to use
     */
    public function setPredicate($predicate) {
        $this->predicate = $predicate;
    }

    /**
     * Get the predicate that would be used right after the 'select' statemet.
     *
     * @return String the predicate
     */
    public function getPredicate() {
        return $this->predicate;
    }

    /**
     * Set a limit on the number of returned records (added at the end of the
     * query).
     */
    public function setLimit($limit) {
        $this->limit = $limit;
    }

    public function getLimit() {
        return $this->limit;
    }

    private function tablesToString() {
        $sql = "";
        foreach ($this->tables as $sqlJoin) {
            if ($sql) {
                $delimiter = $sqlJoin->getCondition() ? " " : ", ";
                $sql .= $delimiter;
            }
            $sql .= $sqlJoin;
        }
        return $sql;
    }
}