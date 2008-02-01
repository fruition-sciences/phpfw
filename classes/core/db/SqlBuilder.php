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

    public function getColumnsString() {
        return arrayToString($this->columns, ",");
    }

    public function __toString()
    {
        $sql = "select " . $this->getColumnsString();
        $sql .= " from " . arrayToString($this->tables, ",");
        if (count($this->conditions) > 0) {
            $sql .= " where " . arrayToString($this->conditions, " and ");
        }
        if ($this->order) {
            $sql .= " order by " . $this->order;
        }
        return $sql;
    }
}