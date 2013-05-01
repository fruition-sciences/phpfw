<?php
/*
 * Created on Aug 17, 2007
 * Author: Yoni Rosenbaum
 */

class Config extends XMLConfig {
    private static $theInstance;

    /**
     * @return Config
     */
    public static function getInstance() {
        if (!self::$theInstance) {
            self::$theInstance = new Config();
        }
        return self::$theInstance;
    }

    protected function getConfigFilePath() {
        return "build/setup/config/config.xml";
    }
}
