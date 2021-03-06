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

    /**
     * @var Aura\Router\Router
     */
    private $router;

    function __construct() {
        $this->form = new Form();
        $this->request = new Request($this);
        $this->ui = $this->newUIManager();
        $this->session = new DefaultSession();
    }

    /**
     * @return Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @return Session
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Set the router. Will be used to generate links.
     *
     * @param Aura\Router\Router $router
     */
    public function setRouter($router) {
        $this->router = $router;
    }

    /**
     * @return \Aura\Router\Router
     */
    public function getRouter() {
        return $this->router;
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

    /**
     * @return Boolean
     */
    public function validateConstraints() {
        if (!isset($_REQUEST['_constraints'])) {
            return true;
        }
        $codedConstraints = $_REQUEST['_constraints'];
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
     * @param string $path Path to the new URL
     * @param Boolean $permanent Determines whether redirection is permanent or not
     */
    public function redirect($path, $permanent=false) {
        $newPath = self::normalizePath($path);
        if ($permanent) {
            header("HTTP/1.1 301 Moved Permanently");
        }
        header('Location: ' . $newPath);
        throw new EndOfResponseException();
    }

    /**
     * Forward to the given View. This is done by throwing a ForwardViewException
     * which is being handled by the Application.
     *
     * @param $view
     */
    public function forward($view) {
        throw new ForwardViewException($view);
    }

    /**
     * If the path starts with '/', make sure it starts with the app root.
     *
     * @param String $path
     * @return String the normalized path.
     */
    public static function normalizePath($path) {
        // If url already starts with the appBase, no need to do anything.
        if (beginsWith($path, Application::getAppRoot())) {
            return $path;
        }
        // Otherwise, if url starts with '/', make sure it is absolute (starting with app root).
        if (beginsWith($path, "/")) {
            return Application::getAppRoot() . substr($path, 1);
        }
        return $path;
    }

    /**
     * @return UI
     */
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

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return Boolean
     */
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
