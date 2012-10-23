<?php
/*
 * Created on Jul 8, 2007
 * Author: Yoni Rosenbaum
 *
 * The main MVC web app.
 * Loads the controller, executes the proper method, and renders the view.
 */

class Application {
    /**
     * @var Context
     */
    private $ctx;
    /**
     * @var string
     */
    private $sessionName;
    /**
     * @var TimeLogger
     */
    private $timeLogger;
    /**
     * @var ITranslator
     */
    private static $translator;

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
            Zend_Session::start();
            $this->includeFiles();
            $transaction = Transaction::getInstance();
            $transaction->setUser($this->getContext()->getUser());
            self::initTranslator($this->getContext()->getUser()->getLocale());
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
            $severity = $e->getSeverity();
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
        $pathInfo = self::getPathInfo();
        $this->timeLogger->setText($pathInfo);
        $tokens = $this->explodePath($pathInfo);
        if (empty($tokens['controller']) && empty($tokens['method'])) {
            /* if there's no controller nor method, we redirect permanently to the default page.
             * If there's a defined locale, we keep it.
             */
            $page = Config::getInstance()->getString('webapp/defaultURL');
            if (substr($page, 0, 1) != '/') {
                $page = '/'. $page;
            }
            if ($tokens['locale'] != null) {
                $page = '/'. $tokens['locale'] . $page;
            }
            $ctx->redirect($page, true);
        }
        try {
            $controllerName = $this->controllerNameFromAlias($tokens['controller']);
        }
        catch (IllegalArgumentException $e) {
            throw new PageNotFoundException($e->getMessage(), 0, $e);
        }
        if (empty($tokens['method'])) {
            $defaultUrl = $ctx->getUIManager()->getDefaultURL();
            $ctx->redirect($defaultUrl);
            return;
        }
        $ctx->setControllerAlias($tokens['controller']);
        $methodName = $tokens['method'];
        // Allow dashes in method name (for SEO purposes). Converts to camelCase.
        $methodName = $this->camelize($methodName);
        $class = new ReflectionClass($controllerName);
        $obj = $class->newInstance();
        if (!$this->checkAccess($class, $obj, $ctx)) {
            return;
        }
        /*
         * If a locale is required, set, but doesn't exist,
        * or if a locale is defined but not required,
        * we say the page doesn't exist and display a 404 error.
        */
        if (
            ($obj->isLocaleSupported() == true && !empty($tokens['locale']) && !in_array($tokens['locale'], self::$translator->getAvailableLocales())) 
            || (!$obj->isLocaleSupported() && !empty($tokens['locale']))
           ) {
            throw new PageNotFoundException(Application::getTranslator()->_("Invalid URL."), 0);
        }
        /*
         * If the controller requires a locale :
         * - If it is not in the url, we redirect the user to his own locale
         * - If it is in the url and the locale is different than the saved locale, we save it in a cookie (for anonymous users only)
         * - We set the locale to the default translator and I18nUtil 
         * Else, we set the user locale to default translator and I18nUtil.
         */
        if ($obj->isLocaleSupported()) {
            if (empty($tokens['locale'])) {
                $ctx->redirect('/'. $this->getSupportedLocale($ctx->getUser()->getLocale()) .'/'. $pathInfo, true);
            }
            if ($ctx->getUser()->isAnonymous() && $ctx->getUser()->getLocale() != $tokens['locale']) {
                $ctx->getUser()->setLocale($tokens['locale']);
                Zend_Session::setOptions(array('cookie_httponly'=>'on'));
                Zend_Session::RememberMe(1209600); // 14 days
            }
            $locale = $tokens['locale'];
        } else {
            $locale = $ctx->getUser()->getLocale();
        }
        I18nUtil::setDefaultLocale($this->getSupportedLocale($locale));
        self::$translator->setLocale($this->getSupportedLocale($locale));
        header('Content-Language: '. self::$translator->getLocale());
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
            if (self::$translator) {
                $view->setTranslator(self::$translator);
            }
            $view->init($ctx);
            global $form;
            $view->render($ctx);
        }
    }
    
     /**
     * Breaks the path info into its various components, which are:
     * - locale
     * - controller : the alias of the controller
     * - method : the controller method to call
     * 
     * The allowed combination of the path info components are:
     * 1. controller/method
     * 2. locale/controller/method 
     * 
     * @param String $pathInfo
     * @return Array a map with the following keys: locale, controller, method.
     */
    private function explodePath($pathInfo) {
        $tokens = explode('/', $pathInfo);
        $params = array(
            'locale' => null,
            'controller' => null,
            'method' => null);
        if (($tokensCount = count($tokens)) == 0) {
            return $params;
        } 
        $posController = 0;
        try {
            $this->controllerNameFromAlias($tokens[0]);
        } catch (IllegalArgumentException $e) {
            if ($tokensCount > 2) {
                $posController = 1;
            }
        }
        $params['controller'] = $tokens[$posController];
        if ($posController != 0) {
            $params['locale'] = $tokens[0];
        }
        if (isset($tokens[$posController + 1]) && !empty($tokens[$posController + 1])) {
            $params['method'] = $tokens[$posController + 1];
        }
        return $params;
    }
    
    /**
     * Get the locale of the user in the given context.
     * If the 'locale' parameter is in the request, sets its value into the user
     * 
     * @param Context $ctx
     */
    public static function getSupportedLocale($locale) {
        $supported_locales = self::$translator->getAvailableLocales();
        if (in_array($locale, $supported_locales)) {
            return $locale;
        }
        $user_locale = explode('_', $locale);
        if (in_array($user_locale[0], $supported_locales)) {
            return $user_locale[0];
        }
        return Config::getInstance()->getString('webapp/defaultLocale', 'en');
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
            $ctx->getSession()->set('user', $ctx->getUser());
        }
        return $ctx;
    }
    /**
     * Create a new User object and fill it with default parameters.
     * The Zend_Locale with no parameter detects automatically the user locale
     */
    private function createAnonymousUser() {
        $user = new User();
        $user->setId(0);
        $timezone = Config::getInstance()->getString("properties/anonymousUserTimezone");
        $user->setTimezone($timezone);
        $zend_locale = new Zend_Locale();
        $user->setLocale($zend_locale->toString());
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
        if (count($result) == 0) {
            throw new ConfigurationException("Missing controllers definition in config file");
        }
        $className = $config->getString("webapp/controllers/controller[@alias='$alias']/@class", null);
        if (!$className) {
            throw new IllegalArgumentException("Unknown alias - " . $alias);
        }
        return $className;
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

    /**
     * Change the given variable name into a camelCase form, (getting rid of
     * dashes).
     * 
     * @param String $varName
     */
    private function camelize($varName) {
        $varName = str_replace('-', ' ', $varName);
        $varName = ucwords($varName);
        $varName = str_replace(' ', '', $varName);
        $varName = lcfirst($varName);
    
        return $varName;
    }

    /**
     * Init the translator of the application.
     * @param $locale Locale to use for the translator
     */
    private static function initTranslator($locale) {
        $translatorClassName = Config::getInstance()->getString('properties/translator', 'I18nUtil');
        if (class_exists($translatorClassName)) {
            $translator = new $translatorClassName();
            if ($translator instanceof ITranslator) {
                self::$translator = $translator;
                self::$translator->setLocale(self::getSupportedLocale($locale));
            } else {
                throw new ConfigurationException("The properties/translator configuration class does not implement ITranslator interface.");
            }
        } else {
            throw new ConfigurationException("The properties/translator configuration parameter is invalid.");
        }
    }
    
    /**
     * Return the translator. If none is set in the first call of getTranslator, it means
     * the application class has not been init, so we set a translator with en_US locale.
     * It can happen by example in scripts ran by run.php, or if Zend_Sessio::start() throws an Exception in Application::service() 
     * @return ITranslator
     */
    public static function getTranslator() {
        if (!(self::$translator instanceof ITranslator)) {
            self::initTranslator('en_US');
        } 
        return self::$translator;
    }
}