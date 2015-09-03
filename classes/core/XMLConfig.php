<?php
/*
 * Base class for XML based config.
 * 
 * Created on Oct 2, 2010
 * Author: Yoni Rosenbaum
 */

abstract class XMLConfig {
    const UNDEFINED = "___UNDEFINED___";
    private $xml; // SimpleXML

    /**
     * No public constructor.
     * Sub-Classes are expected to be singleton objects.
     *  
     * @return XMLConfig
     */
    protected function __construct() {
        $this->load();
    }

    /**
     * Get the path to the config file. The path is relative to the include path.
     *  
     * @return path to config file.
     */
    protected abstract function getConfigFilePath();

    private function load() {
        $xmlStr = FileUtils::getFileContent($this->getConfigFilePath());
        $this->xml = new SimpleXMLElement($xmlStr, LIBXML_NOCDATA);
    }

    public function getString($name, $defaultVal=self::UNDEFINED) {
        $xpath = "/config/$name";
        $result = $this->xml->xpath($xpath);
        if (sizeof($result) > 0) {
            return (string)$result[0];
        }
        if ($defaultVal === self::UNDEFINED) {
            throw new ConfigurationException("Missing configuration value '$name' in " . $this->getConfigFilePath());
        }
        return $defaultVal;
    }

    public function getInt($name, $defaultVal=self::UNDEFINED) {
        return $this->getString($name, $defaultVal);
    }

    public function getBoolean($name, $defaultVal=self::UNDEFINED) {
        $val = strtolower($this->getString($name, $defaultVal));
        if ($val == "true" || $val == "yes" || $val == "1" || $val == "on") {
            return true;
        }
        return false;
    }

    public function get($name) {
        $xpath = "/config/$name";
        $result = $this->xml->xpath($xpath);
        return $result;
    }
}