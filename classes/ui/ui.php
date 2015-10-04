<?php
/*
 * Created on Jun 16, 2007
 * Author: Yoni Rosenbaum
 *
 */

abstract class UI {
    /**
     * @var Context
     */
    protected $ctx;
    /**
     * @var ErrorManager
     */
    private $errorManager;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    /**
     *
     * @param String $title
     * @param String $action Action name
     * @return Button
     */
    public function button($title, $action=null) {
        $button = new Button($title);
        if (isset($action)) {
            $button->setAction($action);
        }
        return $button;
    }

    /**
     *
     * @param String|Href $url
     * @param String $title
     * @return Link
     */
    public function link($url, $title = '') {
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
     * Use the router to generate a new URL to this website.
     * This allows generating a link using a 'routeName' rather than assuming that
     * you know the actual format of the path.
     *
     * @param String $routeName the routing name to use (as defined in routes.yaml)
     * @param Array $data data to use as variables for the route
     * @param Boolean $useCurrentParams if true, uses data from current route and
     *        then overrides with params given by the $data parameter.
     * @return Href
     */
    public function generateURL($routeName, $data=array(), $useCurrentParams=false) {
        $router = $this->ctx->getRouter();
        if ($useCurrentParams) {
            $route = $router->getMatchedRoute();
            $data = array_merge($route->params, $data);
        }
        $url = '/' . $router->generate($routeName, $data);
        return new Href($url);
    }

    /**
     * Use the router to generate a new link.
     *
     * @param String $routeName the routing name to use (as defined in routes.yaml)
     * @param String $title to be used for the link
     * @param Array $data data to use as variables for the route
     * @param Boolean $useCurrentParams if true, uses data from current route and
     *        then overrides with params given by the $data parameter.
     * @return Link
     */
    public function generateLink($routeName, $title='', $data=array(), $useCurrentParams=false) {
        $href = $this->generateURL($routeName, $data, $useCurrentParams);
        return $this->link($href, $title);
    }

    /**
     * Create a new HtmlElement of the given type.
     *
     * @param $type String
     * @return HtmlElement
     */
    public function newHtmlElement($type) {
        $element = new HtmlElement($type);
        return $element;
    }

    /**
     * @return ErrorManager
     */
    public function getErrorManager() {
        if (!$this->errorManager) {
            $this->errorManager = new ErrorManager();
        }
        return $this->errorManager;
    }

    /**
     *
     * @param String $name Name of the table
     * @param String $className Class of the table (default : listStyle1)
     * @return Table
     */
    public function newTable($name, $className='listStyle1') {
        return new Table($name, $className, $this->ctx);
    }

    /**
     *
     * @param String $name Name of the table
     * @param String $className Class of the table (default : css3)
     * @return TableCSS3
     */
    public function newTableCSS3($name, $className='css3') {
        $table = new TableCSS3($name, $className, $this->ctx);
        $table->set('id', $name);
        return $table;
    }

    /**
     * Creates a new Section object
     * @param $title
     * @param $bookmark
     * @return Section
     */
    public function newSection($title='', $bookmark=null) {
        return new Section($title, $bookmark);
    }

    /**
     *
     * @throws ConfigurationException
     * @return string
     */
    public function getDefaultURL() {
        $config = Config::getInstance();
        $result = $config->get('webapp/defaultURL');
        if (sizeof($result) != 1) {
            throw new ConfigurationException("The entry webapp/defaultURL is missing in configuration file.");
        }
        $url = $result[0];
        return (string)$url;
    }

    /**
     * @return Formatter
     */
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
