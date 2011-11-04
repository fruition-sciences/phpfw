<?php
/*
 * Created on Jul 6, 2007
 * Author: Yoni Rosenbaum
 */

require_once("BeanDescriptor.php");
require_once("classes/exception/IllegalStateException.php");

/**
 * Generate a single bean, given a bean descriptor file.
 */
class BeanGenerator {
    private $teampletsDir;
    private $beansOutputDir;

    public function __construct($teampletsDir, $beansOutputDir) {
        $this->teampletsDir = $teampletsDir;
        $this->beansOutputDir = $beansOutputDir;
    }

    public function generate($beanDescriptorFile) {
        global $descriptor;
        $descriptor = new BeanDescriptor($beanDescriptorFile);
        if (!file_exists($this->beansOutputDir)) {
            if (mkdir($this->beansOutputDir)) {
                print "Created directory " . $this->beansOutputDir . "\n";
            }
            else {
                print "Failed to create dir: " .$this->beansOutputDir . "\n";
                return;
            }
        }
        $this->generateBeanBase();
        $this->generateBeanHomeBase();
    }

    private function generateBeanBase() {
        global $descriptor;
        $ctx = array();
        $templateFile = $this->teampletsDir . "/BeanBase.php";
        $template = file_get_contents($templateFile);
        $processedTemplate = $this->processTemplate($template);
        $outFile = $this->beansOutputDir . "/" . $descriptor->xml['name'] . "BeanBase.php";
        file_put_contents($outFile, $processedTemplate);
        print "Wrote file " . $outFile . "\n";
    }

    private function generateBeanHomeBase() {
        global $descriptor;
        $ctx = array();
        $templateFile = $this->teampletsDir . "/BeanHomeBase.php";
        $template = file_get_contents($templateFile);
        $processedTemplate = $this->processTemplate($template);
        $outFile = $this->beansOutputDir . "/" . $descriptor->xml['name'] . "BeanHomeBase.php";
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
