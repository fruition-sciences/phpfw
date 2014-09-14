<?php
/*
 * Created on Sep 26, 2014
 * Author: Yoni Rosenbaum
 *
 */

class MapUtil {
    /**
     * Get the value associated with the given key in the given map.
     *
     * @param $map Map (Associative array)
     * @param $key
     * @param $default (optional) default value to return if the key is not in the map.
     * @return the value, or null if this key is not in the map
     */
    public static function get($map, $key, $default=null) {
        if (array_key_exists($key, $map)) {
            return $map[$key];
        }
        return $default;
    }
}