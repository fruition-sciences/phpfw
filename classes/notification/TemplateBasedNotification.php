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

require_once('Notification.php');

class TemplateBasedNotification extends Notification {
    private $templateFile; // String or Array of Strings
    private $templateContent;
    private $attributes = array();

    /**
     * Construct a new TemplateBasedNotification based on the given template file.
     * The $templateFile parameter is either the path to the template file or
     * array of possible paths. Paths are relative to the 'notifications' directory.
     *
     * @param Mixed $templateFile either a file or an array of possible files.
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

    /**
     * Create the localized string from the content of the template.
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
     * @throws IllegalStateException if a valid template file was not found.
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
}