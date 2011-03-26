<?php
/*
 * Created on Jun 16, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("controls/HtmlElement.php");
require_once("controls/Control.php");
require_once("controls/Button.php");
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
            $button->setAction($action);
        }
        return $button;
    }

    function link($url, $title = '') {
        $href = null;
        if ($url) {
            if (is_object($url) && get_class($url) == 'Href') {
                $href = $url;
            }
            else {
                $href = new Href($url);
            }
        }
        $link = new Link($href, $title);
        return $link;
    }

    /**
     * Create a new HtmlElement of the given type.
     * 
     * @param $type String
     * @return HtmlElement
     */
    function newHtmlElement($type) {
        $element = new HtmlElement($type);
        return $element;
    }

    /**
     * @return ErrorManager
     */
    function getErrorManager() {
        if (!$this->errorManager) {
            $this->errorManager = new ErrorManager();
        }
        return $this->errorManager;
    }

    public function newTable($name, $className='listStyle1') {
        return new Table($name, $className, $this->ctx);
    }

    public function newSection($title='', $bookmark=null) {
        return new Section($title, $bookmark);
    }

    public function getDefaultURL() {
        $config = Config::getInstance();
        $result = $config->get('webapp/defaultURL');
        if (sizeof($result) != 1) {
            throw new ConfigurationException("The entry webapp/defaultURL is missing in configuration file.");
        }
        $url = $result[0];
        return (string)$url;
    }

    public function getFormatter() {
        return Formatter::getInstance();
    }

    /**
     * @return Context
     */
    public function getContext() {
        return $this->ctx;
    }
}

?>
