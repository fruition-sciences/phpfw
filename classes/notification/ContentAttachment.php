<?php
/*
 * Content that can be attached to an email notification.
 * 
 * Created on May 10, 2009
 * Author: Yoni Rosenbaum
 */

class ContentAttachment implements IAttachment {
    private $content;
    private $contentType;
    private $fileName; // A name for the attachment

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function getFileName() {
        return $this->fileName;
    }
}