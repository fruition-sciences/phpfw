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
    private $session;

    function __construct() {
        $this->form = new Form();
        $this->request = new Request($this);
        $this->ui = $this->newUIManager();
        $this->session = new DefaultSession();
    }

    public function getForm() {
        return $this->form;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getSession() {
        return $this->session;
    }

    public function getAttributes() {
        return $this->request->getAttributes();
    }

    /**
     * Check if the given action is the action of the context.
     *
     * @param String $actionName the action name to check
     * @param boolean $allowErrors if false (default), returns false if there are any errors.
     */
    public function actionIs($actionName, $allowErrors=false) {
        $action = $this->getRequest()->getString('_ac', '');
        if ($action === $actionName) {
            return $allowErrors ? true : !$this->form->hasErrors();
        }
        return false;
    }

    public function validateConstraints() {
        $codedConstraints = $this->getRequest()->getString('_constraints', '');
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
        return isset($user) && !$user->isAnonymous();
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