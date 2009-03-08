<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 *
 */


require_once("classes/core/db/db.php");

class Transaction {
    static private $theInstance;
    private $db;
    private $user;

    private function __construct() {
        $this->db = new TheDB();
    }

    public static function getInstance() {
        if (!isset(self::$theInstance)) {
            self::$theInstance = new Transaction();
        }
        return self::$theInstance;
    }

    public function getDB() {
        return $this->db;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function end() {
        $this->db->sql_close();
    }
}