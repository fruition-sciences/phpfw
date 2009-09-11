<?php
/*
 * Created on Sep 11, 2009
 * Author: Yoni Rosenbaum
 *
 * Logs elapsed time into a log file.
 */

class TimeLogger {
    private $startTime;
    private $endTime;
    private $text = "";
    private $logFileName;

    /**
     * Constructor.
     * Should not depend on configuration or anything else. Should just keep start
     * time, so that this class could be called as soon as the application
     * starts.
     * 
     * @param String $logFileName
     * @return TimeLapseLogger
     */
    public function __construct($logFileName) {
        $this->logFileName = $logFileName;
        $this->startTime = microtime(true);
    }

    /**
     * Provide the text that describes what's being measured. This text will
     * show up in the log.
     * 
     * @param $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * Stop the timer and log the ellapsed time into the log file.
     */
    public function end() {
        $this->endTime = microtime(true);
        $this->log();
    }

    private function log() {
        $logDir = Config::getInstance()->getString('logging/logDir');
        $logFile = "$logDir/" . $this->logFileName;
        $fDate = Formatter::dateTimeUTC($this->endTime); 
        $diff = $this->endTime - $this->startTime;

        $fd = fopen($logFile, "a");
        fwrite($fd, "$fDate\t$this->text\t$diff\n");
        fclose($fd);
    }
}