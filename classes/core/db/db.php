<?php
/*
 * Created on Jun 8, 2007
 * Author: Yoni Rosenbaum
 *
 * Copyright � 2007, Zoran Corporation
 * 
 * This software is proprietary to Zoran Corporation and may not be 
 * distributed.
 * 
 * This notice may not be removed or altered from any source distribution.
 */

require_once("SQLException.php");
require_once("ResultSet.php");
require_once("QueryPager.php");

class TheDB {
    private $connect_id;
    private $paging;

    function __construct() {
        $dbHost = Config::getInstance()->getString("database/host");
        $dbUserName = Config::getInstance()->getString("database/userName");
        $dbPassword = Config::getInstance()->getString("database/password");
        $dbDatabaseName = Config::getInstance()->getString("database/dbName");
        $this->sql_connect($dbHost, $dbUserName, $dbPassword, $dbDatabaseName);
    }


    function sql_connect($sqlserver, $sqluser, $sqlpassword, $database){
        $this->connect_id = mysql_connect($sqlserver, $sqluser, $sqlpassword);
        if($this->connect_id){
            if (mysql_select_db($database)){
                return $this->connect_id;
            }else{
                return $this->error();
            }
        }else{
            return $this->error();
        }
    }

    function error(){
        if(mysql_error() != ''){
            echo '<b>MySQL Error</b>: '.mysql_error().'<br/>';
        }
    }

    function query($query, $paging=null) {
        $this->paging = $paging;
        if ($query != NULL) {
            $queryPager = new QueryPager($query, $paging);
            $this->query_result = mysql_query($queryPager->getQuery(), $this->connect_id);
            if(!$this->query_result){
                throw new SQLException(mysql_error() . "\nSQL: " . $query . "\n");
            } else{
                return $this->query_result;
            }
        }else{
            return '<b>MySQL Error</b>: Empty Query!';
        }
    }

    /**
     * Call this method after fetching all rows.
     * With the current implementation, it is important to call this method only
     * when the query was using a PagingInfo object, so that the PagingInfo will
     * contain the total number of rows.
     */
    function disposeQuery() {
        if ($this->paging) {
            $this->paging->setTotalRows($this->getFoundRows());
        }
    }

    function get_num_rows($query_id = ""){
        if($query_id == NULL){
            $return = mysql_num_rows($this->query_result);
        }else{
            $return = mysql_num_rows($query_id);
        }
        if(!$return){
            $this->error();
        }else{
            return $return;
        }
    }

    /**
     * Get the number of rows in the previous query that got executed.
     * This returns the total number of rows even if the query included
     * a 'limit' and thus retreived only a subset of the totel rows.
     * 
     * @return long the total number of rows in the previous query that was
     *         executed.
     */
    function getFoundRows() {
        $this->query("select found_rows()");
        $rs = $this->fetch_row();
        return $rs->getLong(0);
    }


    function fetch_row($query_id = ""){
        if($query_id == NULL){
            $return = mysql_fetch_array($this->query_result);
        }else{
            $return = mysql_fetch_array($query_id);
        }
        if(!$return){
            $this->error();
        }else{
            return new ResultSet($return);
        }
    }
        
    function get_last_id() {
        return mysql_insert_id();
    }

    function get_affected_rows($query_id = ""){
        if($query_id == NULL){
            $return = mysql_affected_rows($this->query_result);
        }else{
            $return = mysql_affected_rows($query_id);
        }
        if(!$return){
            $this->error();
        }else{
            return $return;
        }
    }

    function sql_close(){
        if($this->connect_id){
            return mysql_close($this->connect_id);
        }
    }   
}
?>
