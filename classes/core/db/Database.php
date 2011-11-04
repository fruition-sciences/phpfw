<?php
/*
 * Created on Jun 3, 2011
 * Author: Yoni Rosenbaum
 *
 */

class Database {
    private $mysqli; // The DB connection
    private $paging; // PagingInfo

    public function __construct() {
        $dbHost = Config::getInstance()->getString("database/host");
        $dbUserName = Config::getInstance()->getString("database/userName");
        $dbPassword = Config::getInstance()->getString("database/password");
        $dbDatabaseName = Config::getInstance()->getString("database/dbName");
        
        $mysqli = new mysqli($dbHost, $dbUserName, $dbPassword, $dbDatabaseName);
        if ($mysqli->connect_errno) {
            throw new SQLException("Failed to connect to database: " . $mysqli->connect_error);
        }
    }

    public function query($sql, $paging=null) {
        
    }
}