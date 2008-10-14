<?php
/*
 * Created on Jan 18, 2008
 * Author: Yoni Rosenbaum
 *
 */

class AutoConfig {
    private $propertiesFile;
    public function __construct($propertiesFile) {
        $this->propertiesFile = $propertiesFile;
    }

    public function processDir($templateFile, $outputDir) {
        include($this->propertiesFile);
        $template = 'global $props; ?>' . file_get_contents($templateFile);
        $processedTemplate = $this->processTemplate($template);
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $outFile = $outputDir . "/" . basename($templateFile);
        file_put_contents($outFile, $processedTemplate);
        print "Wrote file " . $outFile . "\n";
    }

    private function processTemplate($template) {
        ob_start();
        eval($template);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}

// Main
if ($argc != 4) {
    echo "missing params\n";
    exit(1);
}

$autoconfig = new AutoConfig($argv[1]);
$autoconfig->processDir($argv[2], $argv[3]);