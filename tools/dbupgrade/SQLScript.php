<?php
/*
 * Created on Oct 28, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class SQLScript {
    private $fileName;
    private $statements = array(); // list of SQLStatement objects

    public function __construct($fileName) {
        $this->fileName = $fileName;
        $this->readFile();
    }

    public function getFileName() {
        return $this->fileName;
    }

    /**
     * Get the list of SQL statements (queries) of this script.
     *
     * @return Array list of SQLStatement objects
     */
    public function getStatements() {
        return $this->statements;
    }

    private function readFile() {
        $content = file_get_contents($this->fileName);
        $statements = split("\\s*;\\s*", $content);
        $i = 0;
        foreach ($statements as $statementContent) {
            $i++;
            $statement = trim($statementContent);
            if ($statement == "") {
                continue;
            }
            $statement = new SQLStatement($i, $statementContent);
            $this->statements[] = $statement;
        }
    }
}

class SQLStatement {
    private $lineNumber;
    private $content;

    public function __construct($lineNumber, $content) {
        $this->lineNumber = $lineNumber;
        $this->content = $content;
    }

    public function getLineNumber() {
        return $this->lineNumber;
    }

    public function getContent() {
        return $this->content;
    }
}