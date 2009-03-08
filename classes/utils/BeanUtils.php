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
}