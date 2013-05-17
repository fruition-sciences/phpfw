<?php
/*
 * Created on May 9, 2009
 * Author: Yoni Rosenbaum
 *
 * Loads a notification from a file. Supports substitutions and custom PHP
 * code within the template.
 *
 * Simple substitutoin is done for all variables of the format: ${varName}.
 * Use the set method to set the value for the variable.
 *
 * In addition to that, php code within the template gets evaluated and executed.
 * This allows more complex coding within the template.
 * Note: the template gets evaluated as PHP code prior to the simple variable
 *       substitution.
 */

class TemplateBasedNotification extends Notification {
    private $templateFile; // String or Array of Strings
    private $templateContent;
    private $attributes = array();

    /**
     * Creates a new TemplateBasedNotification object based on the given template
     * file and the given language code.
     * 
     * Searches for the template file in a few possible locations. If a language
     * is given, searches for the template in a path that includes the language.
     * If language is not given, or if template is not found, looks under no
     * language, and under the application's default language.
     * 
     * Example:
     *   $templateFile = '/path/to/template.php'
     *   $lang = 'fr'
     * 
     *   Will search at:
     *   1. <notification_dir>/fr/path/to/template.php
     *   2. <notification_dir>/path/fr/to/template.php
     *   3. <notification_dir>/path/to/fr/template.php
     *   4. <notification_dir>/path/to/template.php
     *   5. <notification_dir>/en/path/to/template.php
     *   6. <notification_dir>/path/en/to/template.php
     *   7. <notification_dir>/path/to/en/template.php
     *   
     * TODO: Use cache to minimize lookup
     * 
     * @param String $templateFile path to the template, relative to the directory
     *        'application/templates/notifications'.
     * @param String $lang language code
     * @return TemplateBasedNotification
     * @throws FileNotFoundException if the notification template file was not found.
     */
    public static function newInstrance($templateFile, $lang=null) {
        $defaultLang = Config::getInstance()->getString('webapp/defaultLocale', 'en');
        $possibleFiles = array();
        $parts = explode('/', $templateFile);
        if ($lang && $defaultLang != $lang) {
            $possibleFiles = array_merge($possibleFiles, self::injectString($parts, $lang, '/'));
        }
        $possibleFiles[] = $templateFile;
        $possibleFiles = array_merge($possibleFiles, self::injectString($parts, $defaultLang, '/'));
        return new TemplateBasedNotification($possibleFiles);
    }

    /**
     * Construct a new TemplateBasedNotification based on the given template file.
     * The $templateFile parameter is either the path to the template file or
     * array of possible paths. Paths are relative to the 'notifications' directory.
     * 
     * Note: Consider using the method 'newInstrance' instead, in order to get
     *       a template in a given language, if available.
     *
     * @param Mixed $templateFile either a file or an array of possible files.
     * @throws FileNotFoundException if the template file was not found.
     */
    public function __construct($templateFile) {
        $this->templateFile = $templateFile;
        $this->initMessage();
    }

    public function set($key, $value) {
        $this->attributes[$key] = $value;
    }

    public function get($key) {
        return $this->attributes[$key];
    }

    public function setAll($attributes) {
        foreach ($attributes as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Create the localized string from the content of the template.
     * 
     * @throws FileNotFoundException if a valid template file was not found.
     */
    private function initMessage() {
        $file = $this->findTemplateFullPath($this->templateFile);
        $this->templateContent = file_get_contents($file);
    }

    /**
     * Construct the full path to the template (or the possible templates) and
     * return the first valid one.
     *
     * @param Mixed $templateFile String or Array of Strings.
     * @return String full path
     * @throws FileNotFoundException if a valid template file was not found.
     */
    private function findTemplateFullPath($templateFile) {
        $appRoot = Config::getInstance()->getString('appRootDir');
        $pathsArray = (gettype($templateFile) == 'string') ? array($templateFile) : $templateFile;
        $fullPathsArray = array();
        foreach ($pathsArray as $path) {
            $fullPath = "$appRoot/application/templates/notifications/$path";
            if (file_exists($fullPath)) {
                return $fullPath;
            }
            $fullPathsArray[] = $fullPath;
        }
        if (count($fullPathsArray) == 1) {
            $errorMsg = "Can't find notification template file $fullPathsArray[0]";
        }
        else {
            $errorMsg = "Can't find notification template file in any of these paths: ";
            $errorMsg .= implode(', ', $fullPathsArray);
        }
        throw new FileNotFoundException($errorMsg);
    }

    private function processTemplate($template) {
        ob_start();
        eval($template);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    /**
     * Get the final content of this notification, after evaluating the PHP
     * code and substituting variables.
     *
     * @param boolean $reprocess if true, content will be recomputed. Otherwise
     *        template will be processed only the first time.
     * @return String the content
     */
    public function getContent($reprocess=false) {
        if (parent::getContent() === null || $reprocess) {
            // First, process the template to execute any PHP code.
            $preoceesedMessage = $this->processTemplate("?>" . $this->templateContent);

            // Take the result and perform substitution of variables
            $message = new LocalizedString($preoceesedMessage);
            foreach ($this->attributes as $key => $value) {
                $message->set($key, $value);
            }
            parent::setContent($message->__toString());
        }
        return parent::getContent();
    }

    public function newHref($url) {
        $baseURL = Config::getInstance()->getString('properties/serverURL');
        return new Href("$baseURL/$url");
    }

    /**
     * Helper method for creating various variation of a path with the laguage
     * indicator injected in all possible locations.
     *
     * @param String[] $stringArray array of strings (parts of the path)
     * @param String $lang
     * @param String $separator
     * @return String[]
     */
    private static function injectString($stringArray, $lang, $separator) {
        $result = array();
        $len = count($stringArray);
        for ($i=0; $i<$len; $i++) {
            $before = array_slice($stringArray, 0, $i);
            $before[] = $lang;
            $after = array_slice($stringArray, $i, $len-$i);
            $newArray = array_merge($before, $after);
            $str = implode($separator, $newArray);
            $result[] = $str;
        }
        return $result;
    }
}