<?php
/*
 * Created on Jan 22, 2010
 * Author: Yoni Rosenbaum
 *
 */

class PageNotFoundException extends Exception {
    public function __construct($message, $previous=null) {
        parent::__construct($message, $previous);
    }
}