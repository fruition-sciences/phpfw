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
    const LOCK_LENGTH_SECONGS = 7200; // 2 hours.
    private $singleProcess = true; // boolean. If true, only one instance will be permitted.
    private $lockFp;

    /**
     * Execute this application using the given arguments.
     *
     * @param Array $args array of arguments.
     */
    public function execute($args) {
        date_default_timezone_set('UTC');
        $this->includeFiles();
        $this->initLog();
        $this->startTransaction();
        if (!$this->parseArgs($args)) {
            $this->printUsage();
            return;
        }
        if (!$this->lockProcess()) {
            Logger::warning("Process locked. Quitting");
            echo "Process locked. Quitting";
            return;
        }
        $this->init();
        Logger::info("Started");
        $startTime = microtime(true);
        try {
            $this->process();
            $this->onSuccess();
        }
        catch (Exception $e) {
            Logger::error("Exception caught.", $e);
            $this->onException($e);
        }
        $endTime = microtime(true);
        $timeDiff = $endTime - $startTime;
        $this->unlockProcess();

        Logger::info("Completed (" . number_format($timeDiff, 2) . " seconds)");
        $transaction = Transaction::getInstance();
        $transaction->end();
    }

    /**
     * Called after successful call to process()
     */
    protected function onSuccess() {
    }

    /**
     * Called when process() threw an exception.
     * 
     * @param $exception
     */
    protected function onException($exception) {
    }

    /**
     * Parse arguments.
     * Override this if the program's argument do not consist of simple name=val
     * tokens. Otherwise, simply override parseArgKeyValuePair().
     *
     * @param Array $args Array of command line arguments
     * @return boolean true if the arguements are fine. False otherwise.
     */
    protected function parseArgs($args) {
        $argsMap = $this->getKeyValueArgs($args);
        foreach ($argsMap as $key=>$val) {
            if (!$this->parseArgKeyValuePair($key, $val)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse a key value pair (came from command line in the format of key=value).
     * Override this method to validate that all arguments are fine and set
     * values into the class' fields.
     *
     * @param String $key
     * @param String $val
     * @return boolean true if key & value are fine.
     */
    protected function parseArgKeyValuePair($key, $val) {
        return true;
    }

    protected abstract function printUsage();

    /**
     * Overwrite to put any initialization code.
     */
    protected function init() {
    }

    protected abstract function process();

    /**
     * Get the timeout length of the lock file. The lockfile prevents multiple
     * instances of this class to be executed simultaniously. However, the lock
     * file will stop functioning after this number of seconds have passed.
     * The current default number is 120 seconds. Override this method to change
     * it.
     *  
     * @return int number of seconds.
     */
    protected function getLockTimeoutSeconds() {
        return self::LOCK_LENGTH_SECONGS;
    }

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
        $timezone = Config::getInstance()->getString("properties/anonymousUserTimezone");
        $user->setTimezone($timezone);
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
        if (!$lockFile) {
            return false;
        }
        $this->lockFp = @fopen($lockFile, "x");
        if (!$this->lockFp) {
            // Check if the file is too old
            $ctime = filectime($lockFile);
            // If file is old
            if (time() - $ctime > $this->getLockTimeoutSeconds()) {
                // Try deleting file. This will fail if the lock is still in use
                if (!@unlink($lockFile)) {
                    // Delete failed.
                    Logger::warning("Lock file has been locked since " . date("Y-m-d g:i A", $ctime) . " and cannot be removed. Lock file: $lockFile");
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
            echo "creating $lockDir\n";
            $result = mkdir($lockDir, 0777, true);
            if (!$result) {
                Logger::error("Failed to create locks dir $lockDir");
                echo "Lock dir creation failed. See log for details.\n";
                return null;
            }
        }
        $lockFileName = get_class($this) . ".lock";
        $lockFile = "$lockDir/$lockFileName";
        return $lockFile;
    }

    /**
     * Removes the lock file, if one exists.
     */
    private function unlockProcess() {
        // If this process doesn't require locking, don't do anything
        if (!$this->singleProcess) {
            return;
        }
    	$lockFile = $this->getLockFile();
        fclose($this->lockFp);
        if (!@unlink($lockFile)) {
            Logger::warning("Could not delete lock file $lockFile");
        }
    }

    /**
     * Parse each of the input arguments as key value pairs (format is: "name=value").
     * Or a single value.
     * Note: Current implementation does not support multiple args with
     *       the same name.
     *
     * @param Array $args the array of arguments.
     * @return Map a map containing the arguments. Keys are the argument name
     *         and value is the argument's value.
     */
    protected function getKeyValueArgs($args) {
        $map = array();
        foreach ($args as $arg) {
            $tokens = explode("=", $arg);
            $key = $tokens[0];
            $val = count($tokens) > 1 ? $tokens[1] : "";
            $map[$key] = $val;
        }
        return $map;
    }
}