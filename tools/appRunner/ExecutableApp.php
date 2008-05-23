<?php
/*
 * Created on May 21, 2008
 * Author: Yoni Rosenbaum
 * 
 * Each standalone executable needs to extend this abstract class.
 */

abstract class ExecutableApp {
    /**
     * Execute this application using the given arguments.
     * 
     * @@param Array $args array of arguments.
     */
    public function execute($args) {
        $this->initLog();
        $this->startTransaction();
        if (!$this->parseArgs($args)) {
            $this->printUsage();
        }
        $this->process();
    }

    protected abstract function parseArgs();

    protected abstract function printUsage();

    protected abstract function process();

    private function initLog() {
        $errorLogFileName = get_class($this) . ".log";
        $config = Config::getInstance();
        $logDir = $config->getString('logging/logDir'); 
        if ($logDir) {
            $logFile = "$logDir/$errorLogFileName";
            ini_set('error_log', $logFile);
        }        
    }

    private function startTransaction() {
        $transaction = Transaction::getInstance();
        $user = new User();
        // TODO: set id to root.
        $user->setId(1);
        $transaction->setUser($user);
    }
}