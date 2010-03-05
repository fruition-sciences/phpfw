<?php
/*
 * Created on Jul 6, 2007
 * Author: Yoni Rosenbaum
 */

require_once("BeanGenerator.php");
require_once("classes/utils/functions.php");

$generateBeans = new GenerateBeans();
if ($generateBeans->parseArgs()) {
    $generateBeans->process();
}


class GenerateBeans {
    private $beanDescriptorsDir;
    private $templetsDir;
    private $beansOutputDir;

    public function parseArgs() {
        global $argc, $argv;
        if ($argc != 4) {
            usage();
            return false;
        }
        $this->beanDescriptorsDir = $argv[1];
        $this->templetsDir = $argv[2];
        $this->beansOutputDir = $argv[3];
        return true;
    }

    public function process() {
        date_default_timezone_set('UTC');
        $generator = new BeanGenerator($this->templetsDir, $this->beansOutputDir);
        $dirHandle = opendir($this->beanDescriptorsDir);
        while (false !== ($file = readdir($dirHandle))) {
          if (endsWith($file, ".xml")) {
              $descriptorFile = $this->beanDescriptorsDir . "/$file";
              $generator->generate($descriptorFile);
          }
        }        
    }
}

function usage() {
    print "PHP Bean Generator.\n";
    print "Usage: generate.php <descriptors_dir> <templates_dir> <output_dir>\n";
    print "Where:\n";
    print "       descriptors_dir = directory where the bean descriptor XML files are.\n";
    print "       templates_dir = directory where the PHP template files are.\n";
    print "       output_dir = directory where the generated PHP files should be located.\n";
}
