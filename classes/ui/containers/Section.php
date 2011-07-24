<?php
/*
 * Created on Jul 29, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Section extends HtmlElement {
    private $title;
    private $started = false; // true after begin() was called and before end()

    public function __construct($title, $bookmark=null) {
        parent::__construct("fieldset");
        if ($bookmark) {
            $this->set("id", $bookmark);
        }
        $this->title = $title;
    }

    public function begin() {
        echo $this->getElementOpenTag() . "\n";
        $legend = new HtmlElement("legend");
        $legend->setBody($this->title);
        echo $legend;
        $this->started = true;
    }

    public function end() {
        echo $this->getElementCloseTag() . "\n";
        $this->started = false;
    }

    /**
     * Section is not supposed to be printed as a string. This method is overriden
     * and is used for debugging purposes only.
     */
    public function __toString() {
        return "section:" . $this->getName();
    }

    /**
     * Checks whther the section's begin() method has been called (and end()
     * has not been called yet).
     * 
     * @return true is the section has begun. Otherwise false.
     */
    public function isStarted() {
        return $this->started;
    }
}