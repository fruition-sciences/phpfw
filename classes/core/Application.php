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
        http_response_code(500);
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
        $pathInfo = self::getPathInfo();
        $this->timeLogger->setText($pathInfo);

        $ctx = $this->getContext();

        $router = $this->loadRouter();
        $ctx->setRouter($router);

        $route = $router->match($pathInfo, $_SERVER);

        // If no route found, show a 404
        if (!$route) {
            throw new PageNotFoundException(Application::getTranslator()->_("Invalid URL."));
        }

        list($controllerAlias, $methodName, $lang, $redirectPath) = $this->getRouteResult($route);
        Logger::debug("Route: " . $route->name . " (controller=$controllerAlias, method=$methodName, lang=$lang, redirect=$redirectPath)");

        $controllerClassName = null;
        try {
            $controllerClassName = $this->controllerClassNameFromAlias($controllerAlias);
        }
        catch (IllegalArgumentException $e) {
            throw new PageNotFoundException($e->getMessage(), 0, $e);
        }

        $ctx->setControllerAlias($controllerAlias);
        if (!class_exists($controllerClassName)) {
            throw new PageNotFoundException("Controller class not found: $controllerClassName");
        }
        $controller = new $controllerClassName;

        // Check if access is allowed. Controller will redirect if not.
        // TODO: Show a 403 if no access allowed
        if (!$controller->checkAccess($ctx)) {
            header('HTTP/1.1 403 Forbidden');
            return;
        }

        // If locale is required and set, but does not exist throw 404 error
        if ($controller->isLocaleSupported() && $lang && !in_array($lang, self::$translator->getAvailableLocales())) {
            throw new PageNotFoundException(Application::getTranslator()->_("Invalid URL."));
        }

        $locale = $this->getLocale($ctx, $controller, $lang);
        $supportedLocale = $this->getSupportedLocale($locale);

        /**
         * Support the 'redirect' directive of the route.
         * If the route included a 'redirect' value, we redirect to that path,
         * passing all route values + 'lang'.
         */
        if ($redirectPath) {
            $data = array_merge($route->params, array('lang' => $supportedLocale));
            $url = '/' . $router->generate($redirectPath, $data);
            $ctx->redirect($url, true);
        }

        I18nUtil::setDefaultLocale($supportedLocale);
        self::$translator->setLocale($supportedLocale);

        header('Content-Language: '. self::$translator->getLocale());

        // Allow dashes in method name (for SEO purposes). Converts to camelCase.
        $methodName = $this->camelize($methodName);

        if (!method_exists($controller, $methodName)) {
            throw new PageNotFoundException("Missing action method '$methodName' in controller $controllerClassName");
        }
        $view = null;
        // Invoke the controller's method
        try {
            $view = $controller->$methodName($ctx);
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
     * Get the router. Loads from file (routes.yml) if it's not loaded yet.
     *
     * @return Aura\Router\Router
     */
    private function loadRouter() {
        $content = $this->readRouterFile();
        $routerFactory = new Aura\Router\RouterFactory;
        $router = $routerFactory->newInstance();

        foreach ($content['routes'] as $r) {
            $route = $router->add($r['name'], $r['path']);
            if (isset($r['values'])) {
                $route->addValues($r['values']);
            }
            if (isset($r['tokens'])) {
                $route->addTokens($r['tokens']);
            }
        }
        return $router;
    }

    /**
     * Read the content of the router file.
     * Supports both YAML and JSON.
     * If json file exists, uses it.
     * Otherwise, uses yaml file, unless YAML parser is not installed.
     *
     * @return Array map containing the routing content.
     */
    private function readRouterFile() {
        $dir = Config::getInstance()->getString('appRootDir') . '/setup/config';

        // Try json file
        $jsonRoutesFile = "$dir/routes.json";
        if (file_exists($jsonRoutesFile)) {
            $fileContent = file_get_contents($jsonRoutesFile);
            $json = json_decode($fileContent, true);
            if (!$json) {
                throw new ConfigurationException("Invalid JSON content in $jsonRoutesFile");
            }
            return $json;
        }

        $yamlRoutesFile = "$dir/routes.yaml";
        if (!file_exists($yamlRoutesFile)) {
            return $this->getDefaultRouterContent();
        }

        // Check if YAML parser is installed
        if (!function_exists("yaml_parse_file")) {
            // Report missing json routes file
            throw new ConfigurationException("Missing routes file $jsonRoutesFile");
        }

        $content = yaml_parse_file($yamlRoutesFile);
        if (!$content) {
            throw new ConfigurationException("Error reading routing file: $yamlRoutesFile");
        }
        return $content;
    }

    /**
     * For backwards compatibility, provide a default router which supports
     * the two basic routes of:
     * 1.{lang}/{controller}/{action}
     * 2.{controller}/{action}
     *
     * To be used when routing file is missing.
     *
     * @return Array map containing the routing content.
     */
    private function getDefaultRouterContent() {
        return array(
        	  "routes" => array(
        	      array(
        		      "name" => "langLoginAction",
        	        "path" => "{lang}/{controller}/{action}",
        	        "tokens" => array(
        	            "controller" => "login"
        	        )
        	      ),
        	      array(
        	          "name" => "controllerAction",
        	          "path" => "{controller}/{action}"
                )
            )
        );
    }

    /**
     * Get the local. Logic depends on whether the given controller is marked
     * as 'urlLocale' or not.
     *
     * @param Context $ctx
     * @param Controller $controller
     * @param String $lang the language from the URL, or null if it's not there.
     * @return String locale (language)
     */
    private function getLocale($ctx, $controller, $lang) {
        // We only care here about controllers which are marked as 'urlLocale'
        if (!$controller->isLocaleSupported()) {
            return $ctx->getUser()->getLocale();
        }
        if (!$lang) {
            $lang = $ctx->getUser()->getLocale();
        }
        // Update anonymous user's locale, if it's different than the given lang
        if ($ctx->getUser()->isAnonymous() && $ctx->getUser()->getLocale() != $lang) {
            $ctx->getUser()->setLocale($lang);
            // TODO: does this code have to be here??
            Zend_Session::setOptions(array('cookie_httponly'=>'on'));
            Zend_Session::RememberMe(1209600); // 14 days
        }
       return $lang;
    }

    /**
     * Get the controller/action/lang from the given route.
     * lang is optional and may be null.
     *
     * @param Aura\Router\Route $route
     * @return list(String controllerAlias, String methodName, String lang)
     */
    private function getRouteResult($route) {
        $controllerAlias = MapUtil::get($route->params, 'controller');
        $methodName = MapUtil::get($route->params, 'action');
        $lang = MapUtil::get($route->params, 'lang');
        $redirectPath = MapUtil::get($route->params, 'redirect');
        return array($controllerAlias, $methodName, $lang, $redirectPath);
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
     * Returns the given locale, if it is in the list of supported locales.
     * Otherwise, tries extracting the language portion of it (e.g: 'en' from
     * 'en_US') and return it if it is in the list of supported locales.
     * If nothing matches, returns the default locale.
     *
     * @param String $locale
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
     * The returned pathinfo will not start or end with '/'.
     *
     * @return String
     */
    public static function getPathInfo() {
        $appRoot = self::getAppRoot();
        // Get the URL path (everything until the '?')
        $path = MapUtil::get($_SERVER, 'CONTEXT_PREFIX');

        // CONTEXT_PREFIX is aparently new to apache 2.3.13
        // If undefined, fall back to previous method. This one, though, is known
        // to have a problem when the URL is root. ('/').
        if ($path === null) {
            $path = isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['PHP_SELF'];
        }

        $pathInfo = substr($path, strlen($appRoot));
        if (beginsWith($pathInfo, '/')) {
            $pathInfo = substr($pathInfo, 1);
        }
        if (endsWith($pathInfo, '/')) {
            $pathInfo = substr($pathInfo, 0, -1);
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
     *
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

    private function controllerClassNameFromAlias($alias) {
        $config = Config::getInstance();
        $result = $config->get('webapp/controllers/controller');
        if (count($result) == 0) {
            throw new ConfigurationException("Missing controllers definition in config file");
        }
        $className = $config->getString("webapp/controllers/controller[@alias='$alias']/@class", null);

        if (!$className) {
            throw new IllegalArgumentException("Unknown controller alias - " . $alias);
        }
        return $className;
    }

    /**
     * @deprecated remove this one.
     */
    private function controllerNameFromAlias($alias) {
        $config = Config::getInstance();
        $result = $config->get('webapp/controllers/controller');
        if (count($result) == 0) {
            throw new ConfigurationException("Missing controllers definition in config file");
        }
        $className = $config->getString("webapp/controllers/controller[@alias='$alias']/@class", null);
        $namespace = $config->getString("webapp/controllers/controller[@alias='$alias']/@namespace", null);

        if (!$className) {
            throw new IllegalArgumentException("Unknown alias - " . $alias);
        }
        return array($className, $namespace);
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