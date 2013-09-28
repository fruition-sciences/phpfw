<?php
/*
 * Wrapper for database class. (Replaces db.php).
 * Current implementation uses mysqli.
 * 
 * Created on Aug 30, 2013
 * Author: Yoni Rosenbaum
 */
class Database {
    private $db; // mysqli
    private $paging; // PagingInfo
    private $debugOn; // Boolean
    private $queryResult;

    public function __construct() {
        $dbHost = Config::getInstance()->getString("database/host");
        $dbUserName = Config::getInstance()->getString("database/userName");
        $dbPassword = Config::getInstance()->getString("database/password");
        $dbDatabaseName = Config::getInstance()->getString("database/dbName");
        
        $this->db = new mysqli($dbHost, $dbUserName, $dbPassword, $dbDatabaseName);

        $this->debugOn = Config::getInstance()->getBoolean('database/debug', false);
    }

    public function query($sql, $paging=null) {
        if (!$sql) {
            throw new SQLException("Empty query");
        }
        # Keep the PagingInfo so we can set total rows later on.
        $this->paging = $paging;
        $queryPager = new QueryPager($sql, $paging);
        $startTime = microtime(true);
        if ($this->debugOn) {
            Logger::debug("SQL: $sql");
        }
        $this->queryResult = $this->db->query($queryPager->getQuery());
        $endTime = microtime(true);
        if (!$this->queryResult) {
            throw new SQLException($this->db->error . "\nSQL: " . $sql . "\n");
        }
        if ($this->debugOn) {
            $timeDiff = $endTime - $startTime;
            Logger::debug("Query completed in " . number_format($timeDiff, 2) . " seconds.");
        }
        return $this->queryResult;
    }

    /**
     * Prepare the given query for execution.
     * 
     * @param SQLBuilder|String $sqlBuilder can be either:
     *          a. SQLBuilder: in which case it may contain parameters prepared
     *             statements.
     *          b. String: Plain SQL.
     * @param PagingInfo $paging
     * @return mysqli_stmt
     */
    public function execute($sqlBuilder, $paging=null) {
        if (!$sqlBuilder) {
            throw new SQLException("Empty query");
        }
        # Keep the PagingInfo so we can set total rows later on.
        $this->paging = $paging;
        $queryPager = new QueryPager($sqlBuilder, $paging);
        $startTime = microtime(true);
        if ($this->debugOn) {
            Logger::debug("SQL: $sqlBuilder");
        }

        # Create a prepared statement
        $stmt = $this->db->prepare($queryPager->getQuery());

        # Bind parameters, if there are any
        if ($sqlBuilder instanceof SQLBuilder && $sqlBuilder->hasParams()) {
            $refArgs = array($sqlBuilder->getParamTypes());
            foreach ($sqlBuilder->getParamList() as $param) {
                $refArgs[] = $param;
            }
        
            // Modify the values in the array to be referenced (ugly, but works).
            for ($i=1; $i<count($refArgs); $i++) {
                $refArgs[$i] = &$refArgs[$i];
            }

            call_user_func_array(array($stmt, 'bind_param'), $refArgs);
            if ($this->debugOn) {
                Logger::debug("Query params: " . var_export($refArgs, true));
            }
        }

        $stmt->execute();
        $endTime = microtime(true);

        if (!$stmt) {
            throw new SQLException("Failed to execute query: " . $sqlBuilder);
        }

        return $stmt;
    }
    

    /**
     * Call this method after fetching all rows.
     * With the current implementation, it is important to call this method only
     * when the query was using a PagingInfo object, so that the PagingInfo will
     * contain the total number of rows.
     * 
     * For convinience, in case a prepared statement was used, this method also
     * accepts an optional statement, and will close it.
     * 
     * @param mysqli_stmt $statement
     */
    public function disposeQuery($statement=null) {
        if ($statement) {
            $statement->close();
        }
        if ($this->paging) {
            $this->paging->setTotalRows($this->getFoundRows());
        }
        if ($this->queryResult) {
            $this->queryResult->free();
            $this->queryResult = null;
        }
    }

    /**
     * @param resultset $queryResult
     * @deprecated use fetchRow
     */
    public function fetch_row($queryResult=null) {
        return $this->fetchRow($queryResult);
    }

    /**
     * @param resultset $queryResult
     * @return ResultSet
     */
    public function fetchRow($queryResult=null) {
        if (!$queryResult) {
            $queryResult = $this->queryResult;
        } 
        $result = $queryResult->fetch_array();
        if (!$result) {
            return $result;
        }
        return new ResultSet($result);
    }

    /**
     * @deprecated use getLastId
     */
    public function get_last_id() {
        return $this->getLastId();
    }

    public function getLastId() {
        return $this->db->insert_id;
    }

    public function close() {
        return $this->db->close();
    }

    /**
     * Get the internal (mysqli) db.
     * 
     * @return mysqli
     */
    public function getDB() {
        return $this->db;
    }

    /**
     * Get the number of rows in the previous query that got executed.
     * This returns the total number of rows even if the query included
     * a 'limit' and thus retreived only a subset of the totel rows.
     *
     * @return long the total number of rows in the previous query that was
     *         executed.
     */
    private function getFoundRows() {
        $this->query("select found_rows()");
        $rs = $this->fetch_row();
        return $rs->getLong(0);
    }
}