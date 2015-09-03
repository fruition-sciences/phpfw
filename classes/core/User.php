<?php
/*
 * Created on Jul 9, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class User {
    private $id = -1; // UserBean.id
    private $alias;
    private $name;
    private $isAdmin;
    private $groupId;
    /**
     * @var string For example: 'America/Los_Angeles'
     */
    private $timezone;
    /**
     * @var string For example: 'en_US'
     */
    private $locale = 'en_US';

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

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }

    public function getGroupId() {
        return $this->groupId;
    }


    public function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /** 
     * @return String
     */
    public function getLocale() {
        return $this->locale;
    }

    public function isAnonymous() {
        return $this->id <= 0;
    }

}
