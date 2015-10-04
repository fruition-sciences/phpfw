<?php
/*
 * Created on Jul 7, 2007
 * Author: Yoni Rosenbaum
 *
 */

class SQLBuilder {
    private $command = "select";
    private $tables = array();
    private $columns = array();
    private $conditions = array();
    private $params = array(
    	'list' => array(), // Array of parameters used by prepapred statement
        'types' => ''      // String containing sequence of types. See types under http://www.php.net/manual/en/mysqli-stmt.bind-param.php
    );
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

    public function delete($tableName) {
        $this->from($tableName, '');
        $this->command = "delete";
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
    public function addColumns($alias, $columns, $functions=null) {
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

    /**
     * Add a condition to the 'where' clause of the query.
     * All filters are treated as 'AND', and each will be surrounded by
     * parentheses for protection.
     *
     * Use 'varTypes' and arg1, arg2,... for prepapred statements with bound parameters.
     * Note that you can pass extra parameters after arg1.
     *
     * Example:
     *  sqlBuilder.where('name=? and age=?', 'si', 'john', 11);
     *
     * @param string $condition the SQL clause
     * @param string $varTypes defines the types of parameters you pass.
     *               Each character represents the type of the proceeding args.
     *               Supported types are: (See: http://www.php.net/manual/en/mysqli-stmt.bind-param.php)
     *               - s : String
     *               - i : Integer
     *               - d : Double
     *               - b : Blob
     *               - a : Array. (to be use in 'in (?)' clause). The type of each param will be evaluated at runtime.
     *               - t : timestamp. Will be converted to date-time in GMT.
     * @param string $arg1 the first parameter.
     * @param string $arg2,... the method accepts additional parameters. Number
     *               of arguments should match the length of the string $varTypes.
     */
    public function filter($condition, $varTypes=null, $arg1=null) {
        // Call collectParams, passing all parameters
        $condition = call_user_func_array(array($this, 'collectParams'), func_get_args());
        $this->conditions[] = "($condition)";
    }

    /**
     * Use this method in order to perform an explicit regular (inner) join.
     * You can also achieve an inner join using 'select', 'from' and 'filter'.
     *
     * Support for prepared statement is limited:
     * 1. Variables (i.e: the '?' symbol) can be used only in the tableName, not
     *    in the condition. This is useful when the tableName is actually an
     *    inner query.
     * 2. Make sure to call 'innerJoin' method(s) *before* calling 'filter'.
     *    That is because in the final SQL query, the conditions of the filters
     *    appear after the joins. If you 'filter' before 'innerJoin', the order
     *    of the parameters of the prepared statement will be incorrect.

     *
     * @param string $tableName
     * @param string $alias
     * @param string $condition
     * @param string $columns (optional) columns to be selected
     * @param string $varTypes defines the types of parameters you pass.
     *               @see filter
     * @param string $arg1 the first parameter.
     * @param string $arg2,... the method accepts additional parameters. Number
     *               of arguments should match the length of the string $varTypes.
     */
    public function join($tableName, $alias, $condition, $columns=null, $varTypes=null, $arg1=null) {
        // We just call explicitJoin with joinType=INNER_JOIN
        $params = array($tableName, $alias, $condition, SQLJoin::INNER_JOIN);
        // Add optional parameters to params list
        if (func_num_args() > 3) {
            $params = array_merge($params, array_slice(func_get_args(), 3));
        }
        call_user_func_array(array($this, 'explicitJoin'), $params);
    }

    /**
     * Adds a left join to the query.
     * Example:
     * leftJoin('students', 's', 's.id = x.student_id')
     *
     * Support for prepared statement is limited. @see join
     *
     * @param string $tableName
     * @param string $alias
     * @param string $condition
     * @param string $columns (optional) columns to be selected
     * @param string $varTypes defines the types of parameters you pass.
     *               @see filter
     * @param string $arg1 the first parameter.
     * @param string $arg2,... the method accepts additional parameters. Number
     *               of arguments should match the length of the string $varTypes.
     */
    public function leftJoin($tableName, $alias, $condition, $columns=null, $varTypes=null, $arg1=null) {
        // We just call explicitJoin with joinType=LEFT_JOIN
        $params = array($tableName, $alias, $condition, SQLJoin::LEFT_JOIN);
        // Add optional parameters to params list
        if (func_num_args() > 3) {
            $params = array_merge($params, array_slice(func_get_args(), 3));
        }
        call_user_func_array(array($this, 'explicitJoin'), $params);
    }

    /**
     * Collects the parameters to be used in prepared statement.
     * All the parameters are collected into $this->params and should correspond
     * to the '?' symbols in the given SQL.
     *
     * In case any of the variables where an array, the corresponding '?' symbol
     * in the given SQL clause will be expanded into multiple '?' symbols
     * according to the size of the array.
     *
     * @param string $sql SQL clause
     * @param string $varTypes defines the types of parameters you pass.
     *               @see filter
     * @param string $arg1 the first parameter.
     * @param string $arg2,... the method accepts additional parameters. Number
     *               of arguments should match the length of the string $varTypes.
     * @return string the given SQL clause
     */
    private function collectParams($sql, $varTypes=null, $arg1=null) {
        if ($varTypes) {
            if (func_num_args()-2 != strlen($varTypes)) {
                throw new IllegalArgumentException("Number of variables must match the length of the varTypes variable: '$varTypes'");
            }

            // This map will indicate which of the variables is an array and
            // what its size is.
            $arraySizesMap = array();

            foreach (str_split($varTypes) as $i => $varType) {
                // Get the argument
                $arg = func_get_arg($i + 2);

                // Handle 'array'
                if ($varType == 'a') {
                    if (!is_array($arg)) {
                        throw new IllegalArgumentException("Type of argument number $i is expected to be array. Value is $arg");
                    }
                    // Add each of the items and their type
                    foreach ($arg as $item) {
                        $this->params['list'][] = $item;
                        $this->params['types'] .= $this->getBindVariableType($item);
                    }
                    // Mark that this arg is an array and keep its size
                    $arraySizesMap[$i] = count($arg);
                }
                // Handle 'timestamp'
                else if ($varType == 't') {
                    $this->params['list'][] = SQLUtils::convertDate($arg, 'GMT', false);
                    $this->params['types'] .= 's';
                }
                else {
                    $this->params['list'][] = $arg;
                    $this->params['types'] .= $varType;
                }
            }
            // In case of array vars, expand their '?' to a comma-separated list of '?'
            $sql = $this->expandArrayVars($sql, $arraySizesMap);
        }
        return $sql;
    }

    /**
     * Performs an expilicit join.
     * Supports parameters for prepared statement.
     * Parameters can be refered by either the 'condition' or in the
     * 'tableName', which is useful in case of a join with an inner query.
     * Note: Currently, support for array parameters is available only if it
     * appears in the 'tableName', not in the 'condition'.
     *
     * @param $tableName
     * @param $alias
     * @param $condition
     * @param $joinType int self::INNER_JOIN or self::LEFT_JOIN
     * @param $columns (optional) columns to be selected
     * @param string $varTypes defines the types of parameters you pass.
     *               @see filter
     * @param string $arg1 the first parameter.
     * @param string $arg2,... the method accepts additional parameters. Number
     *               of arguments should match the length of the string $varTypes.
     */
    public function explicitJoin($tableName, $alias, $condition, $joinType, $columns=null, $varTypes=null, $arg1=null) {
        // If there are parameters for prepared statement (param #6 and above)
        if (func_num_args() > 5) {
            $params = array($tableName);
            $params = array_merge($params, array_slice(func_get_args(), 5));
            $tableName = call_user_func_array(array($this, 'collectParams'), $params);
        }

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
        $sql = $this->command . " ";
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

    /**
     * Check whether this builder contains parameters for a prepared statement.
     *
     * @return boolean
     */
    public function hasParams() {
        return count($this->params['list']) > 0;
    }

    /**
     * Get the list of parameters for a prepapred statement.
     *
     * @return array
     */
    public function getParamList() {
        return $this->params['list'];
    }

    /**
     * Get a string which represents the types of the parameters for a prepared
     * statement. See types under http://www.php.net/manual/en/mysqli-stmt.bind-param.php
     *
     * @return string
     */
    public function getParamTypes() {
        return $this->params['types'];
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

    /**
     * Get the binding type suitable for the given variable.
     * See: http://www.php.net/manual/en/mysqli-stmt.bind-param.php
     *
     * @param unknown $var
     * @return string
     */
    private function getBindVariableType($var) {
        if (is_string($var)) {
            return 's';
        }
        if (is_integer($var)) {
            return 'i';
        }
        if (is_double($var)) {
            return 'd';
        }
        return 'b';
    }

    /**
     * Given a SQL clause with potentially multiple '?', this method replaces
     * certain occurances of '?' with a comma-separated list of '?'. It does so
     * only for occurances which are indicated by the given map.
     * For example: If $arraySizesMap[2] == 5 then the 3rd '?' will be replaced
     * with '?,?,?,?,?'.
     *
     * @param string $condition
     * @param Map $arraySizesMap indicates which indexes of occurances of '?' in
     *        the given condition represents an array, and what the array size is.
     * @return string the given condition, with some of its '?' potentially
     *         replaced with comma-separated list of '?'.
     */
    private function expandArrayVars($condition, $arraySizesMap) {
        if (empty($arraySizesMap)) {
            return $condition;
        }
        $parts = explode('?', $condition);
        $newCondition = '';
        foreach ($parts as $i => $part) {
            // Paste the '?' (or its replacement by multiple '?") before the content
            // but only after the first one.
            if ($i > 0) {
                $qMarks = '?';
                // If $arraySizesMap contains this index, replace '?' with comma-separated list of '?'.
                if (isset($arraySizesMap[$i-1])) {
                    $qMarks = implode(',', array_fill(0, $arraySizesMap[$i-1], '?'));
                }
                $newCondition .= $qMarks;
            }
            $newCondition .= $part;
        }
        return $newCondition;
    }
}