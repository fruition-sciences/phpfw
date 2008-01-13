<?php
/*
 * Created on Jul 29, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Section extends HtmlElement {
    private $title;

    public function __construct($title) {
        parent::__construct("fieldset", "section");
        $this->title = $title;
    }

    public function begin() {
        echo $this->getElementOpenTag() . "\n";
        $legend = new HtmlElement("legend");
        $legend->setBody($this->title);
        echo $legend;
    }

    public function end() {
        echo $this->getElementCloseTag() . "\n";
    }

    /**
     * Section is not supposed to be printed as a string. This method is overriden
     * and is used for debugging purposes only.
     */
    public function __toString() {
        return "section:" . $this->getName();
    }
}