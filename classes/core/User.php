<?php
/*
 * Created on Jul 9, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class User {
    private $id; // UserBean.id
    private $alias;
    private $name;
    private $isAdmin;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setIsAdmin($isAdmin) {
    	$this->isAdmin = $isAdmin;
    }

    public function isAdmin() {
    	return $this->isAdmin;
    }
}