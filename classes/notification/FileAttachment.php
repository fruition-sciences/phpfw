<?php
/*
 * A file that can be attached to an email notification.
 * 
 * Created on May 10, 2009
 * Author: Yoni Rosenbaum
 */

require_once('IAttachment.php');

class FileAttachment implements IAttachment {
    private $filePath; // Path to the file

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }
    
    public function getFilePath() {
        return $this->filePath;
    }
}