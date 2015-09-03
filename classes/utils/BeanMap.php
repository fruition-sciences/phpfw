<?php
/*
 * Created on Apr 3, 2009
 * Author: Yoni Rosenbaum
 *
 * Map of Beans (assumed to be of the same type). Keys are IDs by default, but
 * can ben overriden by specifying the $keyMethodName.
 *
 * Allows lookup by the bean itself or by the key (as specified by $keyMethodName)
 */

class BeanMap {
    private $map = array(); // id -> Bean
    private $keyMethodName;

    /**
     * Construct a new BeanMap.
     * The parameter $keyMethodName defines which medhod to call in order to
     * get the key to map by. By default, uses the 'getId' method.
     * If the value returnd by this method is not unique, the map will keep
     * the last bean that was added.
     *
     * @param array $beansArray
     * @param String $keyMethodName (optional)
     */
    public function __construct($beansArray=null, $keyMethodName='getId') {
        $this->keyMethodName = $keyMethodName;
        if ($beansArray) {
            $this->setAll($beansArray);
        }
    }

    /**
     * If $forceSet=true, this method behaves as a common 'set' method - it sets
     * the given bean into the map (key is the value returned by keyMethodName),
     * and returns it.
     * However, if $forceSet=false, it will set the bean into the map *only* if
     * it is not there yet, and will then return the bean from the map.
     *
     * @param BeanBase $bean the bean to set
     * @param boolean $forceSet if false, bean will be set only if it's not yet in the map.
     * @return BeanBase the bean (either the existing one or the given one, depending on the $forceSet parameter).
     */
    public function set($bean, $forceSet=false) {
        $existingBean = $this->get($bean);
        if ($forceSet || !$existingBean) {
            $key = $this->getKey($bean);
            $this->map[$key] = $bean;
            $existingBean = $bean;
        }
        return $existingBean;
    }

    public function setAll($beansArray, $forceSet=false) {
        foreach ($beansArray as $bean) {
            $this->set($bean, $forceSet);
        }
    }

    /**
     * Lookup in the map for the bean associated with the key of the given bean.
     * The key used is the value of the method $keyMethodName, which is by default
     * the method getId.
     *
     * @return BeanBase the bean from the map, or null if it's not there.
     */
    public function get($bean) {
        $key = $this->getKey($bean);
        if (isset($this->map[$key])) {
            return $this->map[$key];
        }
        return null;
    }

    public function getById($id) {
        if (isset($this->map[$id])) {
            return $this->map[$id];
        }
        return null;
    }

    /**
     * Return the full map array
     * @return Array id -> bean
     */
    public function getAll(){
        return $this->map;
    }


    /**
     * Return the 'plain', i.e. numerically indexed, array of all beans in the map.
     *
     * @return Array of beans
     */
    public function getAllAsList(){
        return array_values($this->map);
    }

    /**
     * Get the keys of the map.
     * If the map was constructed using the default $keyMethodName then these
     * keys will be the ids of the beans.
     *
     * @return Array of ids
     */
    public function getIds() {
        return array_keys($this->map);
    }

    private function getKey($bean) {
        return call_user_func(array($bean, $this->keyMethodName));
    }
}
