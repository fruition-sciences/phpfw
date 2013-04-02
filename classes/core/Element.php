<?php
/*
 * Created on Jun 22, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Element {
    protected $atts = array();

    /**
     * 
     * @param String $key
     * @param String $val
     * @return Element
     */
    public function set($key, $val=null) {
        $this->atts[$key] = $val;
        return $this;
    }

    /**
     * 
     * @param String $key
     * @return String|NULL
     */
    public function get($key) {
        return isset($this->atts[$key]) ? $this->atts[$key] : null;
    }

    /**
     *
     * @param String $key
     * @return Element
     */
    public function un_set($key) {
        unset($this->atts[$key]);
        return $this;
    }

    /**
     * Remove all attributes.
     * 
     * @return Element
     */
    public function removeAll() {
        $this->atts = array();
        return $this;
    }
}