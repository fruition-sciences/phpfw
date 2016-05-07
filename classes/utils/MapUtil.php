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

    /**
     * Group (split) the given list of objects by their given method.
     * Each item in the list is assumed to be an object which has a method with
     * the given name.
     * The result is a map (associative array) where the key is value returned
     * by the method and the value is an array of objects.
     *
     * If the optional $valueCallback function is provided, then the items in
     * each array in the returned map would be the result of a call to this
     * function (with each item as parameter), rather than the items themselves.
     *
     * @param Object[] $list
     * @param String $methodName
     * @param function $valueCallback (optional)
     */
    public static function groupBy($list, $methodName, $valueCallback=null) {
        $map = array();
        foreach ($list as $item) {
            $value = $valueCallback ? $valueCallback($item) : $item;
            $key = call_user_func(array($item, $methodName));
            if (isset($map[$key])) {
                $map[$key][] = $value;
            } else {
                $map[$key] = array($value);
            }
        }
        return $map;
    }
}