<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 *
 */

require_once("classes/ui/View.php");
require_once("classes/ui/Template.php");
require_once("classes/ui/Page.php");
require_once("classes/ui/ui.php");
require_once("classes/ui/Form.php");
require_once("classes/core/Href.php");
require_once("classes/ui/controls/Link.php");
require_once("classes/ui/Formatter.php");
require_once("classes/ui/DataConverter.php");

abstract class BaseView implements View {
    /**
     * @var Template
     */
    protected $template;
    private $components = array(); // Map component_name -> component
    /**
     * @var Context
     */
    private $ctx;
    /**
     * @var Page
     */
    private $page;
    /**
     * @var ITranslator
     */
    private $translator = null;

    /**
     * @param Context $ctx
     */
    public function init($ctx) {
        $this->ctx = $ctx;
        $this->prepare($ctx);
        $this->initChildComponents($ctx);
    }

    /**
     * @param Context $ctx
     */
    public function prepare($ctx) {
    }
    
    /**
     * @param Context $ctx
     */
    public function render($ctx) {
        $this->ctx = $ctx;
        $path = $this->getTemplateDirPath() . "/" . $this->getTemplateName();
        global $form, $format;
        $form = $ctx->getForm();
        $timezone = $ctx->getUser()->getTimezone();
		if(!$timezone) {
        	$timezone = Config::getInstance()->getString("properties/anonymousUserTimezone");
		}
        $format = $ctx->getUIManager()->getFormatter();
        // Make $page and $ui globals, so they can be accessed by the view template.
        global $page, $ui;
        $page = $this->getPage();
        $ui = $this->ctx->getUIManager();
        include($path);
    }

    public function getTemplateName() {
        return get_class($this) . ".php";
    }

    public function getTemplateDirPath() {
        return "application/templates/controller/" . $this->ctx->getControllerAlias();
    }

    protected function getContext() {
        return $this->ctx;
    }

    protected function getTemplate() {
        if (!isset($this->template)) {
            $this->template = new Template($this->getPage());
        }
        return $this->template;
    }

    protected function get($key, $default='__UNDEFINED__') {
        if ($default != '__UNDEFINED__' && !$this->getTemplate()->containsKey($key)) {
            return $default;
        }
        return $this->getTemplate()->get($key);
    }

    protected function containsKey($key) {
        return $this->getTemplate()->containsKey($key);
    }

    public function addComponent($name, $component) {
        $this->components[$name] = $component;
        if ($component instanceof View) {
            $component->setTranslator($this->translator);
        }
        $component->setParentView($this);
    }

    /**
     * Render the child component with the given name.
     * @param String $name the component's name
     */
    public function component($name) {
        $this->components[$name]->show();
    }

    public function getComponent($name) {
        return $this->components[$name];
    }

    public function hasComponent($name) {
        return isset($this->components[$name]);
    }

    private function initChildComponents($ctx) {
        foreach ($this->components as $name=>$component) {
            $component->setTranslator($this->translator);
            $component->init($ctx);
        }
    }

    public function getPage() {
        if (!$this->page) {
            $this->page = $this->newPage($this->translator);
            $this->page->ctx = $this->ctx;
        }
        return $this->page;
    }

    protected function newPage(ITranslator $translator=null) {
        global $ui;
        $ui = $this->ctx->getUIManager();
        return $ui->newPage($translator);
    }
    
    /**
     * Set a translation module : $translator must be
     * an instance of a class which implements ITranslator
     * @param ITranslator $translator
     */
    public function setTranslator(ITranslator $translator) {
        $this->translator = $translator;
        return $this;
    }
    
    public function getTranslator() {
        return $this->translator;
    }
    
    /**
     * Return the translated sentence
     * @param string $sentence
     * @return string
     */
    public function _($sentence) {
        if ($this->translator === null) {
            return $sentence;
        }
        return $this->translator->_($sentence);
    }

    public function __toString() {
        return get_class($this);
    }
}
