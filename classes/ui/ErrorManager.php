<?php
/*
 * Created on Jul 14, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class ErrorManager {
    private $errors = array(); // List of UserError objects
    private $tagsMap = array(); // tag (String) -> UserError

    public function addError($tag) {
        $error = new UserError();
        $this->tagsMap[$tag] = $error;
        $this->errors[] = $error;
    }

    public function addErrorMessage($msg) {
        $error = new UserError();
        $error->setMessage($msg);
        $this->errors[] = $error;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setErrorMessage($tag, $message) {
        if (!isset($this->tagsMap[$tag])) {
            return;
        }
        $error = $this->tagsMap[$tag];
        $error->setMessage($message);
    }
}

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