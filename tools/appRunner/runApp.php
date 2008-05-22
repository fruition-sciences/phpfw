<?php
/*
 * Created on May 21, 2008
 * Author: Yoni Rosenbaum
 * 
 * Runs a class that extends ExecutableApp.
 * This script is called by the run.php script, which defines the proper include
 * path. 
 */

require_once("include/classes.php");
require_once("ExecutableApp.php");

class AppRunner {
    private $thisFileName;
    private $appClass; // Class name to execute.
    private $appDirPath; // Directory where php app is in. (relative to 'apps' dir)
    private $appArgs;

    public function process($args) {
        if (!$this->parseArgs($args)) {
            $this->printUsage();
            return false;
        }
        $this->executeApp();
        return true;
    }

    private function executeApp() {
        $appFullPath = $this->getAppFullPath();
        if (!file_exists($appFullPath)) {
            throw new FileNotFoundException("Class file doesn't exist: " . $appFullPath);
        }
        require_once($appFullPath);
        $class = new ReflectionClass($this->appClass);
        $obj = $class->newInstance();
        $obj->execute($this->appArgs);
    }

    private function getAppFullPath() {
        $appRoot = Config::getInstance()->getString('appRootDir');
        if ($this->appDirPath) {
            return "$appRoot/application/classes/apps/$this->appDirPath/$this->appClass.php";
        }
        return "$appRoot/application/classes/apps/$this->appClass.php";
    }

    /**
     * Parse the given arguments.
     *
     * @param Array $args
     * @return boolean true if args are OK. Otherwise false.
     */
    private function parseArgs($args) {
        if (count($args) < 2) {
            return false;
        }
        $this->parseAppClassName($args[1]);
        $this->appArgs = array_slice($args, 2);
        return true;
    }

    private function parseAppClassName($arg) {
        if (endsWith($arg, "/")) {
            throw new IllegalArgumentException("Invalid class name: $arg");
        }
        if (endsWith($arg, ".php")) {
            $arg = substr($arg, 0, sizeof($this->appFile) - 4);
        }
        
        $pos = strrpos($arg, '/');
        if ($pos === false) {
            $this->appClass = $arg;
        }
        else {
            $this->appDirPath = substr($arg, 0, $pos);
            $this->appClass = substr($arg, $pos+1);
        }
    }

    private function printUsage() {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        echo "Usage: $scriptName <phpAppFile>\n";
        echo "\n";
        echo "Where:\n";
        echo "       phpAppClass = The php class to execute.\n";
    }
}

$appRunner = new AppRunner();
if ($appRunner->process($argv)) {
    exit(0);
}
else {
    exit(1);
}