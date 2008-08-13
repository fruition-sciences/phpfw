<?php
/*
 * Created on Jul 7, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class SqlBuilder {
    private $tables = array();
    private $columns = array();
    private $conditions = array();
    private $joins = array();
    private $order = "";
    private $group = "";
    private $predicate;
    private $limit;

    public function select($tableName, $alias, $columns) {
        $this->from($tableName, $alias);
        foreach ($columns as $column) {
            $this->columns[] = "${alias}.${column} ${alias}_${column}";
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
        $this->tables[] = "${tableName} ${alias}";
    }

    public function filter($condition) {
        $this->conditions[] = "($condition)";
    }

    public function leftJoin($tableName, $alias, $condition) {
        $this->joins[] = " left join " . $tableName . " " . $alias . " on ($condition)";
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
        $sql .= " from " . arrayToString($this->tables, ",");
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
}