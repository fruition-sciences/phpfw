<?php
/*
 * Created on Apr 30 2013
 * Author: bsoufflet
 * 
 */

class UserError {
    private $fieldName;
    private $message;

    public function setFieldName($fieldName) {
        $this->fieldName = $fieldName;
    }

    public function getFieldName() {
        return $this->fieldName;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getMessage() {
        return $this->message;
    }

    public function __toString() {
        return $this->message;
    }
}