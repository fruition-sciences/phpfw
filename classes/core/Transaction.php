<?php
/*
 * Created on Jul 20, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Transaction {
    /**
     * @var Transaction
     */
    static private $theInstance;
    /**
     * @var Database
     */
    private $db;
    /**
     * @var User
     */
    private $user;

    private function __construct() {
        $this->db = new Database();
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
     * @return Database
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