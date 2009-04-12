<?php
/*
 * Created on Apr 3, 2009
 * Author: Yoni Rosenbaum
 *
 * Map of Beans (assumed to be of the same type). Keys are IDs.
 * Allows lookup by the bean itself.
 */

class BeanMap {
    private $map = array(); // id -> Bean

    /**
     * If $forceSet=true, this method behaves as a common 'set' method - it sets
     * the given bean into the map (key is id), and returns it.
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
            $this->map[$bean->getId()] = $bean;
            $existingBean = $bean;
        }
        return $existingBean;
    }

    /**
     * Get from the map the bean with an id equals to the id of the given bean.
     *
     * @return BeanBase the bean from the map, or null if it's not there.
     */
    public function get($bean) {
        if (isset($this->map[$bean->getId()])) {
            return $this->map[$bean->getId()];
        }
        return null;
    }
}