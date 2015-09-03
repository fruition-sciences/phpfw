<?php
/*
 * Created on Nov 1, 2008
 * Author: Yoni Rosenbaum
 *
 */

class BeanUtils {
    public static function getBeanIds($beans) {
        $ids = array();
        foreach ($beans as $bean) {
            $ids[] = $bean->getId();
        }
        return $ids;
    }

    /**
     * Apply the given method on each of the given beans.
     * 
     * @param Bean[] $beans
     * @param String $getterMethodName
     * @return Array array containing the values returned by each of the calls.
     */
    public static function getValues($beans, $getterMethodName) {
    	$values = array();
    	foreach ($beans as $bean) {
    		$values[] = call_user_func(array($bean, $getterMethodName));
    	}
    	return $values;
    }
}