<?php
/**
 * Created on Apr 30 2013
 * @author bsoufflet
 *
 * Holds a join, which can be either:
 * 1. Implicit inner join (regular join)
 *    or
 * 2. Explicit join. Either inner join or left join.
 * 
 * In case of an explicit join, the condition will be populated.
 */
class SQLJoin {
    const INNER_JOIN = 1;
    const LEFT_JOIN = 2;

    private $table;
    private $alias;
    private $condition; // If set, an explicit join notation will be used
    private $joinType; // self::INNER_JOIN or self::LEFT_JOIN

    public function __construct($table, $alias, $condition=null, $joinType=self::INNER_JOIN) {
        $this->table = $table;
        $this->alias = $alias;
        $this->condition = $condition;
        $this->joinType = $joinType;
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
            $joinTypeStr = $this->joinType == self::LEFT_JOIN ? 'left' : '';
            $sql = "$joinTypeStr join $sql on ($this->condition)";
        }
        return $sql;
    }
}