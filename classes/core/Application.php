<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 *
 * The main MVC web app.
 * Loads the controller, executes the proper method, and renders the view.
 */

class Application {
    private $ctx;
    private $sessionName;
    private $timeLogger;

    public function init() {
        // Register error handler
        //set_error_handler(array($this, 'errorHandler'));
        // Register shutdown function - allow detecting more errors
        //register_shutdown_function(array($this, 'shutdown'));
        $this->timeLogger = new TimeLogger('phpfw-timer/phpfw-timer.log');
        $config = Config::getInstance();
        $logDir = $config->getString('logging/logDir');
        if (!$logDir) {
            throw new ConfigurationException("Configuration value 'logging/logDir' is empty");
        }
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0777, true)) {
                echo "Failed to create dir: $logDir";
            }
        }
        $errorLogFileName = $config->getString('webapp/logging/errorLogFileName');
        if ($logDir && $errorLogFileName) {
            $logFile = "$logDir/$errorLogFileName";
            ini_set('error_log', $logFile);
        }
        date_default_timezone_set('UTC');
        $this->sessionName = 'phpfw' . Config::getInstance()->getString('appRoot');
    }

    public function service() {
        try {
            session_name($this->sessionName);
            session_start();
            $this->includeFiles();
            $this->validate();
            $this->invokeControllerMethod();
        }
        catch (Exception $e) {
            $this->handleException($e);
        }
        $logTimeEnabled = Config::getInstance()->getBoolean('webapp/logging/timeLog/enabled', false);
        if ($logTimeEnabled) {
            $this->timeLogger->end();
        }
    }

    /**
     * Final handling of the exception.
     * Logs the error and renders the error page.
     * 
     * Note: All PHP errors are being caught by the Application and then
     * being passed as exceptions to this method.
     * 
     * @param $e
     */
    private function handleException($e) {
        // Variables set in this method can be accessed by the template.
        if ($e instanceof ErrorException) {
            $severety = $e->getSeverity();
        }
        if ($e instanceof EndOfResponseException) {
            // Happens after redirect. Ignore this exception.
            return;
        }
        $showErrorDetails = Config::getInstance()->getBoolean('webapp/errorHandling/showDetails', true);
        // Page not found
        if ($e instanceof PageNotFoundException) {
            header("HTTP/1.0 404 Not Found");
            include("www/templates/page_not_found.php");
            return;
        }
        // For all other errors - show error page.
        Logger::error($e);
        include("www/templates/error.php");
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
        $this->timeLogger->setText($pathInfo);
        $tokens = explode('/', $pathInfo);
        if (sizeof($tokens) < 2 || $tokens[1] === '') {
            $defaultUrl = $ctx->getUIManager()->getDefaultURL();
            $ctx->redirect($defaultUrl);
            return;
        }
        try {
            $controllerName = $this->controllerNameFromAlias($tokens[0]);
        }
        catch (IllegalArgumentException $e) {
            throw new PageNotFoundException($e->getMessage(), 0, $e);
        }
        $ctx->setControllerAlias($tokens[0]);
        $methodName = $tokens[1];
        $class = new ReflectionClass($controllerName);
        $obj = $class->newInstance();
        if (!$this->checkAccess($class, $obj, $ctx)) {
            return;
        }
        try {
            $method = $class->getMethod($methodName);
        }
        catch (ReflectionException $e) {
            throw new PageNotFoundException($e->getMessage(), 0, $e);
        }
        try {
            $view = $method->invoke($obj, $ctx);
        }
        catch (ForwardViewException $e) {
            // Hanlde 'forwarding': A controller method threw this exception
            // containing a view instead of returning it in a normal way.
            $view = $e->getView();
        }
        if ($view instanceof View) {
            if ($ctx->getUIManager()->getErrorManager()->hasErrors()) {
                $ctx->getForm()->setValues($ctx->getAttributes());
            }
            $view->init($ctx);
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
        $path = isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['PHP_SELF'];
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
    /**
     * Build the page URL (http + serverName + port) from the $_SERVER
     * php variable. There is no / at the end.
     * the app root is not appended.
     * @return String page URL
     */
    public static function getPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")$pageURL .= "s";
        $pageURL .= "://".$_SERVER["SERVER_NAME"];
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= ":".$_SERVER["SERVER_PORT"];
        }
        return $pageURL;
    }

    private function getContext() {
        if (!$this->ctx) {
            $this->ctx = $this->createContext();
        }
        return $this->ctx;
    }

    private function createContext() {
        $ctx = new Context();
        if ($ctx->getSession()->hasKey('user')) {
            $user = $ctx->getSession()->get('user');
            $ctx->setUser($user);
        }
        else {
            $ctx->setUser($this->createAnonymousUser());
        }
        return $ctx;
    }

    private function createAnonymousUser() {
        $user = new User();
        $user->setId(0);
        $timezone = Config::getInstance()->getString("properties/anonymousUserTimezone");
        $user->setTimezone($timezone);
        return $user;
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

    private function includeFiles() {
        $includer = new Includer();
        $includer->includeAll();
    }

    /**
     * Allow overwriting the session name. If not called, session name is
     * constructed based on the application name (taken from config).
     *
     * @param String $sessionName session name to use
     */
    public function setSessionName($sessionName) {
        $this->sessionName = $sessionName;
    }

    /**
     * Catch errors that would normally not throw and exception.
     * This does not catch all errors, just some of them.
     * Examples of error that gets caught here:
     *   - Accessing undefined index in array.
     *   - Warnings
     *
     * @param $errno the level of the error, as defined in: http://www.php.net/manual/en/errorfunc.constants.php
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return unknown_type
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        $errorLevel = $this->getConfigStopErrorLevel();
        if ($errno & $errorLevel) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        // Log warning if necessary
        $reportLevel = $this->getConfigReportErrorLevel();
        if ($errno & $reportLevel) {
            Logger::warning($errstr . " in $errfile:$errline");
        }
    }

    /**
     * A way to detects errors that cannot be caught using the set_error_handler
     * method.
     * This method is called at the end of each php request. If there was an
     * error, it calls the error handler.
     */
    public function shutdown() {
        $error = error_get_last();
        if ($error) {
            // We cannot throw exception from here. Pass it to our exception handler method.
            $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            $this->handleException($e); 
        }
    }

    /**
     * Get the 'stop' error level from configuration.
     * This is the biwise combination of error levels upon which the application
     * should stop.
     *  
     * @return long
     */
    private function getConfigStopErrorLevel() {
        $exp = Config::getInstance()->getString('webapp/errorHandling/stopLevel', 'E_ALL');
        eval("\$level = $exp;");
        return $level;
    }

    /**
     * Get the 'report' error level from configuration.
     * This is the biwise combination of error levels that the application
     * should log.
     * All other errors will be ignored.
     *  
     * @return long
     */
    private function getConfigReportErrorLevel() {
        $exp = Config::getInstance()->getString('webapp/errorHandling/reportLevel', 'E_ALL | E_STRICT');
        eval("\$level = $exp;");
        return $level;
    }
}