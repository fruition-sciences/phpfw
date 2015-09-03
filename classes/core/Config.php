<?php
/*
 * Created on Aug 17, 2007
 * Author: Yoni Rosenbaum
 */

class Config extends XMLConfig {
	/**
	 * 
	 * @var Config
	 */
    private static $theInstance;
    /**
     *  Define if it is testing en environment or not
     *  
     * @var boolean $isTestConfig
     */
    private static $isTestConfig = false;

    /**
     * 
     * @param boolean $isTestConfig
     * @return Config
     */
    public static function getInstance($isTestConfig = false) {
    	self::$isTestConfig = $isTestConfig;
        if (!self::$theInstance) {
            self::$theInstance = new Config();
        }
        return self::$theInstance;
    }

    protected function getConfigFilePath() {
        if(self::$isTestConfig) {
            return "tests/config/config.xml";
        }
        else {
            return "build/setup/config/config.xml";
        }
    }
}
