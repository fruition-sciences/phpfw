<?php
/*
 * Created on Aug 17, 2007
 * Author: Yoni Rosenbaum
 */

class Config {
    const UNDEFINED = "___UNDEFINED___";

    private static $theInstance;
    private $xml; // SimpleXML
    const configFile = "build/setup/config/config.xml";
//    private $configArray = array();

    private function __construct() {
        $this->load();
    }
    
    /**
     * @return Config
     */
    public static function getInstance() {
        if (!self::$theInstance) {
            self::$theInstance = new Config();
        }
        return self::$theInstance;
    }

    private function load() {
        $xmlStr = FileUtils::getFileContent(self::configFile);
        $this->xml = new SimpleXMLElement($xmlStr);
    }

    public function getString($name, $defaultVal=self::UNDEFINED) {
        $xpath = "/config/$name";
        $result = $this->xml->xpath($xpath);
        if (sizeof($result) > 0) {
            return (string)$result[0];
        }
        if ($defaultVal === self::UNDEFINED) {
            throw new ConfigurationException("Missing configuration value '$name' in " . self::configFile);
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

