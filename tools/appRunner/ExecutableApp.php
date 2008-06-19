<?php
/*
 * Created on May 21, 2008
 * Author: Yoni Rosenbaum
 * 
 * Each standalone executable needs to extend this abstract class.
 * By default, only one ExecutableApp of a kind can be executed at a time. This
 * concurrency checking is based on lock files.
 * To allow multiple instances, call setSingleProcess(false).
 */

abstract class ExecutableApp {
    const LOCK_LENGTH_SECONGS = 60; // 1 hour
    private $singleProcess = true; // boolean. If true, only one instance will be permitted.
    private $lockFp;

    /**
     * Execute this application using the given arguments.
     * 
     * @param Array $args array of arguments.
     */
    public function execute($args) {
        $this->includeFiles();
        $this->initLog();
        $this->startTransaction();
        if (!$this->parseArgs($args)) {
            $this->printUsage();
            return;
        }
        if (!$this->lockProcess()) {
            Logger::warning("Process locked. Quitting");
            return;
        }
        Logger::info("Started");
        $startTime = microtime(true);
        try {
            $this->process();
        }
        catch (Exception $e) {
            Logger::error("Exception caught.", $e);
        }
        $endTime = microtime(true);
        $timeDiff = $endTime - $startTime;
        $this->unlockProcess();
        
        Logger::info("Completed (" . number_format($timeDiff, 2) . " seconds)");
    }

    /**
     * Parse arguments.
     * 
     * @param Array $args Array of command line arguments
     * @return boolean true if the arguements are fine. False otherwise.
     */
    protected abstract function parseArgs($args);

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

    private function includeFiles() {
        $includer = new Includer();
        $includer->includeAll();
    }

    /**
     * Set weather only one process of this script can be executed at a time.
     * 
     * @param boolean $singleProcess
     */
    public function setSingleProcess($singleProcess) {
        $this->singleProcess = $singleProcess;
    }

    /**
     * If this ExecutableApp requires locking ($sigleProcess=true), the process
     * will be locked by writing a 'lock' file to the disk. If a lock file
     * already exists, returns false.
     * 
     * @return boolean true if this application doesn't require locking or if
     *         locking was successful. If locking failed, returns false.
     */
    private function lockProcess() {
        // If this process doesn't require locking, return true.
        if (!$this->singleProcess) {
            return true;
        }

        $lockFile = $this->getLockFile();
        $this->lockFp = @fopen($lockFile, "x");
        if (!$this->lockFp) {
            // Check if the file is too old
            $ctime = filectime($lockFile);
            // If file is older than 2 hours
            if (time() - $ctime > self::LOCK_LENGTH_SECONGS) {
                // Try deleting file
                if (!@unlink($lockFile)) {
                    // Delete failed.
                    Logger::warning("Lock file has been locked since " . date("Y-m-d g:i A", $ctime) . " an cannot be removed. Lock file: $lockFile");
                    return false;
                }
                Logger::warning("Deleted old lock file from " . date("Y-m-d g:i A", $ctime));
                // Try locking again
                $this->lockFp = @fopen($lockFile, "x");
                if (!$this->lockFp) {
                    return false;
                }
            }
            else { // File is not old
                return false;
            }
        }
        return true;
    }

    /**
     * Get the full path to the lock file. (the file may not exist).
     * If the directory doesn't exist, this method will create it.
     * 
     * @return resource a file handle
     */
    private function getLockFile() {
        $lockDir = Config::getInstance()->getString("properties/storageDir") . "/locks";
        if (!is_dir($lockDir)) {
            mkdir($lockDir);
        } 
        $lockFileName = get_class($this) . ".lock";
        $lockFile = "$lockDir/$lockFileName";
        return $lockFile;
    }

    /**
     * Removes the lock file, if one exists. 
     */
    private function unlockProcess() {
        $lockFile = $this->getLockFile();
        fclose($this->lockFp);
        unlink($lockFile);
    }
}