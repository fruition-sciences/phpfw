<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class Application {
    private $ctx;

    public function service() {
        try {
            session_name('phpfw');
            session_start();
            $this->includeFiles();
            $this->validate();
            $this->invokeControllerMethod();
        }
        catch (Exception $e) {
            include("www/templates/error.php");
            echo "<p><b>Error details:</b></p>";
            echo "<pre>";
            echo $e;
            echo "</pre>";
        }
    }

    private function validate() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {
            $ctx = $this->getContext();
            $ctx->validateConstraints();
        }
    }

    private function invokeControllerMethod() {
        $ctx = $this->getContext();
        $transaction = Transaction::getInstance();
        $transaction->setUser($ctx->getUser());
        $pathInfo = self::getPathInfo(); 
        $tokens = explode('/', $pathInfo);
        if (sizeof($tokens) < 2 || $tokens[1] === '') {
            $defaultUrl = $this->getDefaultUrl();
            $ctx->redirect($defaultUrl);
        }
        $controllerName = $this->controllerNameFromAlias($tokens[0]);
        $ctx->setControllerAlias($tokens[0]);
        $methodName = $tokens[1];
        $class = new ReflectionClass($controllerName);
        $obj = $class->newInstance();
        if (!$this->checkAccess($class, $obj, $ctx)) {
            return;
        } 
        $method = $class->getMethod($methodName);
        $view = $method->invoke($obj, $ctx);
        if (is_a($view, 'View')) {
            $view->init($ctx);
            if ($ctx->getForm()->hasErrors()) {
                $ctx->getForm()->setValues($ctx->getAttributes());
            }
            global $form;
            $view->render($ctx);
        }
    }

    /**
     * Get the path info, which is everything that follows the application
     * node in the URL. (without query info).
     * The returned pathinfo will not start with '/'.
     */
    public static function getPathInfo() {
        $appRoot = self::getAppRoot();
        $path = $_SERVER['PHP_SELF'];
        $pathInfo = substr($path, strlen($appRoot));
        if (beginsWith($pathInfo, '/')) {
            $pathInfo = substr($pathInfo, 1);
        }
        return $pathInfo;
    }

    public static function getAppRoot() {
        $appRoot = Config::getInstance()->getString("appRoot");
        if (!endsWith($appRoot, '/')) {
            $appRoot = $appRoot . '/';
        }
        return $appRoot;
    }

    private function getContext() {
        if (!$this->ctx) {
            $this->ctx = $this->createContext();
        }
        return $this->ctx;
    }

    private function createContext() {
        $ctx = new Context();
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $ctx->setUser($user);
        }
        return $ctx;
    }

    /**
     * Check if access is allowed to the given controller class, with the given
     * context. This is done by calling the 'checkAccess' method of the controller.
     * If not allowed, this method may redirect to another page.
     *
     * @param Controller $controllerClass the controller class
     * @param Controller $controller the controller object
     * @param Context $ctx the context
     * @return boolean whether access is allowed.
     */
    private function checkAccess($controllerClass, $controller, $ctx) {
        $method = $controllerClass->getMethod('checkAccess');
        return $method->invoke($controller, $ctx);
    }

    private function controllerNameFromAlias($alias) {
        $config = Config::getInstance();
        $result = $config->get('webapp/controllers/controller');
        if (sizeof($result) == 0) {
            throw new ConfigurationException("Missing controllers definition in config file");
        }
        foreach ($result as $controllerEntry) {
            if ($alias == $controllerEntry['alias']) {
                $className = (string)$controllerEntry['class'];
                return $className;
            } 
        }
        throw new IllegalArgumentException("Unknown alias - " . $alias);
    }

    private function getDefaultUrl() {
        $config = Config::getInstance();
        $result = $config->get('webapp/defaultURL');
        if (sizeof($result) != 1) {
            throw new ConfigurationException("The entry webapp/defaultURL is missing in configuration file.");
        }
        $url = $result[0];
        return $url;
    }

    private function includeFiles() {
        $includer = new Includer();
        $includer->includeAll();
    }
}