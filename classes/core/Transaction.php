<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 *
 */


require_once("classes/core/db/db.php");

class Transaction {
    /**
     * @var Transaction
     */
    static private $theInstance;
    /**
     * @var TheDB
     */
    private $db;
    /**
     * @var User
     */
    private $user;

    private function __construct() {
        $this->db = new TheDB();
    }

    /**
     * @return Transaction
     */
    public static function getInstance() {
        if (!isset(self::$theInstance)) {
            self::$theInstance = new Transaction();
        }
        return self::$theInstance;
    }

    /**
     * @return TheDB
     */
    public function getDB() {
        return $this->db;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    public function end() {
        $this->db->sql_close();
    }
}