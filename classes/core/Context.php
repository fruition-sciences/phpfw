<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Context {
    private $form;
    private $user;
    private $controllerAlias;
    private $request;
    private $ui;

    function __construct() {
        $this->form = new Form();
        $this->request = new Request($this);
        $this->ui = $this->newUIManager();
    }

    public function getForm() {
        return $this->form;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getAttributes() {
        return $this->request->getAttributes();
    }

    public function actionIs($actionName) {
        $action = $this->getRequest()->getString('_ac', '');
        if ($action === $actionName) {
            return !$this->form->hasErrors();
        }
        return false;
    }

    public function validateConstraints() {
        $codedConstraints = $this->getRequest()->getString('_constraints', null);
        if (!$codedConstraints) {
            return true;
        }
        $constraints = unserialize(base64_decode($codedConstraints));
        $ok = true;
        foreach ($constraints as $constraint) {
            $ok = $constraint->validate($this) && $ok;
        } 
        return $ok;
    }

    /**
     * Redirect to another URL. If the given path starts with '/', the path is
     * assumed to be absolute under this application.
     */
    public function redirect($path) {
        $newPath = self::normalizePath($path);
        header('Location: ' . $newPath);
        return null;
    }

    public static function normalizePath($path) {
    	return beginsWith($path, "/") ? Application::getAppRoot() . substr($path, 1) : $path;
    }

    public function getUIManager() {
        return $this->ui;
    }

    public function getAppAlias() {
        return Application::getAppRoot();
    }

    public function setControllerAlias($controllerAlias) {
        $this->controllerAlias = $controllerAlias;
    }

    public function getControllerAlias() {
        return $this->controllerAlias;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

    public function isUserLoggedIn() {
        $user = $this->getUser();
        return isset($user);
    }
    

    private function newUIManager() {
        $config = Config::getInstance();
        $result = $config->get('webapp/uiManagerClass');
        if (sizeof($result) != 1) {
            throw new ConfigurationException("The entry webapp/uiManagerClass is missing in configuration file.");
        }
        $uiManagerClassName = (string)$result[0];
        $class = new ReflectionClass($uiManagerClassName);
        $obj = $class->newInstance($this);
        return $obj;
    }
}