<?php
/*
 * Created on Aug 17, 2007
 * Author: Yoni Rosenbaum
 */

class Config extends XMLConfig {
    private static $theInstance;
    private static $context = false;

    /**
     * @return Config
     */
    public static function getInstance($context = false) {
    	self::$context = $context;
        if (!self::$theInstance) {
            self::$theInstance = new Config();
        }
        return self::$theInstance;
    }

    protected function getConfigFilePath() {
        if(self::$context) {
            return "tests/config/config.xml";
        }
        else {
            return "build/setup/config/config.xml";
        }

    }
}
