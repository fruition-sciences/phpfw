<?php
/**
 * Created on Mar 11, 2015
 * Author: Sidiki Coulibaly
 *
 * This dummy class is used only to test MapUtils functions
 */

abstract class BlockKcBeanBase extends BeanBase {
    // Fields
    private $id = -1;

    /**
     * @return id 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param id $id
     */
    public function setId($id) {
        $this->id = $id;
    }
}