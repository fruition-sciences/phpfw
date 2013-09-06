<?php
/*
 * Created on Oct 28, 2007
 * Author: Yoni Rosenbaum
 * 
 */

require_once "classes/utils/functions.php";
require_once "SQLScript.php";
require_once "classes/core/db/Database.php";
require_once "classes/core/db/QueryPager.php";
require_once "classes/core/db/ResultSet.php";
require_once "classes/core/Transaction.php";
require_once "classes/core/XMLConfig.php";
require_once "classes/core/Config.php";
require_once "classes/core/Logger.php";
require_once "classes/exception/FileNotFoundException.php";
require_once "classes/utils/FileUtils.php";
require_once "classes/core/db/SQLException.php";

$upgradeDb = new UpgradeDB();
if ($upgradeDb->parseArgs()) {
    $success = $upgradeDb->process();
    if (!$success) {
        exit(1);
    }
}

class UpgradeDB {
    private $scriptsXmlFile;

    public function parseArgs() {
        global $argc, $argv;
        if ($argc != 2) {
            usage();
            return false;
        }
        $this->scriptsXmlFile = $argv[1];
        return true;
    }

    /** 
     * @return boolean true on success, false if there was an error.
     */
    public function process() {
        $ret = true;
        $xmlElement = simplexml_load_file($this->scriptsXmlFile);
        $baseDir = dirname($this->scriptsXmlFile);
        $db = Transaction::getInstance()->getDB();
        try {
            foreach ($xmlElement->script as $scriptElement) {
                $script = new SQLScript($baseDir, $scriptElement['file']);
                if (!$this->scriptWasExecuted($script)) {
                    $this->executeScript($script);
                }
            }
        }
        catch (SQLException $e) {
            echo $e;
            $ret = false;
        }
        $db->close();
        return $ret;
    }

    private function executeScript($script) {
        $db = Transaction::getInstance()->getDB();
        try {
            echo "executing " . $script->getFileFullPath() . "\n";
            foreach ($script->getStatements() as $statement) {
                $db->query($statement->getContent());
            }
            $this->markScriptExecuted($script);
        }
        catch (SQLException $e) {
            echo "Error on script " . $script->getFileFullPath() . " in statement starting on line " . $statement->getLineNumber() . "\n";
            throw $e;
        }
        catch (FileNotFoundException $e) {
            echo "Script file not found: " . $script->getFileFullPath() . "\n";
            throw $e;
        }
    }

    /**
     * Check in the database if the given script had already been executed.
     *
     * @return boolean whether the given script had already been executed.
     */
    private function scriptWasExecuted($script) {
        $db = Transaction::getInstance()->getDB();
        $sql = "select 1 from db_script where path = '" . $script->getFileRelPath() . "'";
        $db->query($sql);
        return $db->fetch_row() ? true : false; 
    }

    private function markScriptExecuted($script) {
        $db = Transaction::getInstance()->getDB();
        $sql = "insert into db_script (path, create_date) values ('" . $script->getFileRelPath() . "', SYSDATE())";
        $db->query($sql);
    }
}

function usage() {
    print "DB Upgrader. This program runs necessary DB migration scripts.\n";
    print "Usage: UpgradeDB.php <scripts_file>\n";
    print "Where:\n";
    print "       scripts_file = the xml file that lists the scripts to execute.\n";
}
