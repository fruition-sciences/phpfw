<?php
/*
 * Created on Jan 18, 2008
 * Author: Yoni Rosenbaum
 *
 * Processes a template file(s) by substituting variables with values defined
 * in a properties file.
 * The properties file is a php file which defines a global map named $props.
 */

class AutoConfig {
    private $propertiesFile;
    private $templateFile; // null if $templateDir is available
    private $templateDir;  // null if $templateFile is available
    private $outputDir;

    public function parseArgs($args) {
        while ($arg = next($args)) {
            if ($arg == '-p') {
                $this->propertiesFile = next($args);
                if (!$this->propertiesFile) {
                    return $this->printUsage("Missing value for -p argument");
                }
                continue;
            }
            if ($arg == '-tf') {
                $this->templateFile = next($args);
                if (!$this->templateFile) {
                    return $this->printUsage("Missing value for -tf argument");
                }
                continue;
            }
            if ($arg == '-td') {
                $this->templateDir = next($args);
                if (!$this->templateDir) {
                    return $this->printUsage("Missing value for -td argument");
                }
                continue;
            }
            if ($arg == '-o') {
                $this->outputDir = next($args);
                if (!$this->outputDir) {
                    return $this->printUsage("Missing value for -o argument");
                }
                continue;
            }
            else {
                return $this->printUsage("Invalid argument: $arg");
            }
        }
        return $this->validateVars();
    }

    private function validateVars() {
        if (!$this->templateFile && !$this->templateDir) {
            return $this->printUsage("Either -tf or -td must be used");
        }
        if (!$this->outputDir) {
            return $this->printUsage("The -o flag is missing");
        }
        if (!$this->outputDir) {
            return $this->propertiesFile("The -p flag is missing");
        }
        return true;
    }

    public function process() {
        // Include the properties file, to make $props available as a global var
        require($this->propertiesFile);
        if ($this->templateFile) {
            $this->processTemplateFile($this->templateFile);
        }
        else {
            $this->processTemplateDir($this->templateDir);
        }
    }

    private function processTemplateDir($templateDir) {
        $dirHandle = @opendir($templateDir);
        if (!$dirHandle) {
            return;
        }
        while (false !== ($file = readdir($dirHandle))) {
            $path = "$templateDir/$file";
            if (!is_file($path)) {
                continue;
            }
            $this->processTemplateFile($path);
        }
        closedir($dirHandle);
    }

    private function processTemplateFile($templateFile) {
        $template = 'global $props; ?>' . file_get_contents($templateFile);
        $processedTemplate = $this->processTemplateContent($template);
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }
        $outFile = $this->outputDir . "/" . basename($templateFile);
        file_put_contents($outFile, $processedTemplate);
        print "Wrote file " . $outFile . "\n";
    }

    private function processTemplateContent($template) {
        ob_start();
        eval($template);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function printUsage($errorMessage) {
        echo "AutoConfig: $errorMessage\n\n";
        echo "AutoConfig: Processes a template file (or directory) using data from a properties file to\n" .
                "produce a new file (or set of files).\n\n";
        echo "Usage: AutoConfig.php [options]\n";
        echo "Where options are:\n";
        echo "  -p props_file : Properties file to use.\n";
        echo "  -tf template_file : Template file. Cannot be used with the -td option.\n";
        echo "  -td template_dir : Template directory. Cannot be used with the -tf option.\n";
        echo "  -o output_dir : Output directory.\n";
        return false;
    }
}

// Main
$autoconfig = new AutoConfig();
if ($autoconfig->parseArgs($argv)) {
    $autoconfig->process();
}
else {
    exit(1);
}
