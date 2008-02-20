<?php
/*
 * Created on Feb 19, 2008
 * Author: Yoni Rosenbaum
 * 
 */

interface Session {
    /**
     * Check if the session contains the given key.
     * 
     * @param String $key the key to check.
     */
    public function hasKey($key);

    /**
     * Set the given key/value pair into the session.
     * 
     * @param String $key the key
     * @param Object $value the value to associate with the key.
     */
    public function set($key, $value);

    /**
     * Remove the given key from the session.
     * 
     * @param String $key the key to remove from the session.
     */
    public function un_set($key);

    /**
     * Get the value associated with the given key
     * @param String $key the key to find in the session.
     * @param String $defaultValue value to return in case the key doesn't exist.
     * @return Object the value associated with the given key, or null if it
     *         is not defined.
     */
    public function get($key, $defaultValue=null);
}