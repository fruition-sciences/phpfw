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

    public function hasErrors() {
        return count($this->errors) > 0;
    }
}