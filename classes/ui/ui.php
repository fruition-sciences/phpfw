<?php
/*
 * Created on Jun 16, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once("controls/Button.php");
require_once("controls/HtmlElement.php");
require_once("containers/Table.php");
require_once("containers/Section.php");
require_once("ErrorManager.php");

abstract class UI {
    protected $ctx;
    private $errorManager;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }   

    function button($title, $action=null) {
        $button = new Button($title);
        if (isset($action)) {
            $button->set_action($action);
        }
        return $button;
    }

    function link($url, $title = '') {
        $href = null;
        if ($url) {
            if (get_class($url) == 'Href') {
                $href = $url;
            }
            else {
                $href = new Href($url);
            }
        }
        $link = new Link($href, $title);
        return $link;
    }

    function newHtmlElement($type) {
        $element = new HtmlElement($type);
        return $element;
    }

    function getErrorManager() {
        if (!$this->errorManager) {
            $this->errorManager = new ErrorManager();
        }
        return $this->errorManager;
    }

    public function newTable($name, $className='listStyle1') {
        return new Table($name, $className, $this->ctx);
    }

    public function newSection($title='') {
        return new Section($title);
    }
}

?>
