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
    private $order = "";
    private $group = "";
    private $predicate;
    private $limit;

    /**
     * Add a select for the given column and apply the given functions on the column
     * @param $tableName
     * @param $alias prefix to be used for all column aliases.
     * @param $columns
     * @param $functions (optional) Array of the same size as $columns which
     *        contains SQL functions (such as max, avg, min) to apply on the
     *        corresponding column. A null entry in the array means no function
     *        should be apply on corresponding column.
     */
    public function select($tableName, $alias, $columns, $functions=null) {
        $this->from($tableName, $alias);
        $useFunction = is_array($functions) && count($columns) == count($functions);
        foreach ($columns as $k=>$column) {
            if($useFunction && !empty($functions[$k])){
                $this->columns[] = "{$functions[$k]}(${alias}.${column}) {$functions[$k]}_${alias}_${column}";
            }else{
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

    public function leftJoin($tableName, $alias, $condition) {
        $this->tables[$alias] = new SQLJoin($tableName, $alias, $condition);
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

/**
 * Holds either a regular table join, or a left join (in which case a condition
 * will be populated).
 */
class SQLJoin {
    private $table;
    private $alias;
    private $condition; // Used for left joins only

    public function __construct($table, $alias, $condition=null) {
        $this->table = $table;
        $this->alias = $alias;
        $this->condition = $condition;
    }

    public function getTable() {
        return $this->table;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getCondition() {
        return $this->condition;
    }

    public function __toString() {
        $sql = "$this->table $this->alias";
        if ($this->condition) {
            $sql = "left join $sql on ($this->condition)";
        }
        return $sql;
    }
}