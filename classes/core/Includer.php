<?php
/*
 * Created on Dec 28, 2007
 * Author: Yoni Rosenbaum
 *
 */

class Includer {
    private static $theInstance;

    public static function getInstance() {
        if (!self::$theInstance) {
            self::$theInstance = new Config();
        }
        return self::$theInstance;
    }

    public function includeAll() {
        $config = Config::getInstance();
        $baseDir = $config->getString('appRootDir');
        $result = $config->get('webapp/include/dir');
        foreach ($result as $dirElement) {
            $dir = (string)$dirElement;
            $path = $baseDir . '/' . $dir;
            $this->includeDir($path);
        }
    }

    public function includeDir($dir) {
        $dirHandle = @opendir($dir);
        if (!$dirHandle) {
            return;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if (endsWith($file, ".php")) {
                $path = $dir . "/$file";
                require_once($path);
            }
        }
        closedir($dirHandle);
    }
}