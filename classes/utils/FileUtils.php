<?php
/*
 * Created on Oct 5, 2007
 * Author: Yoni Rosenbaum
 * 
 */

class FileUtils {
    /**
     * Get the content of the given file path as a string.
     * The file can be a relative path, on the include path.
     *
     * @param String filename the path to the file.
     */
    public static function getFileContent($filename) {
        ob_start();
        readfile($filename, true);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    /**
     * Retrieve a list of directories of a given directory
     * @param String $directoryPath
     * @return Array List of directories
     */
    function dir_list($directoryPath){
        $l = array();
        $files = array_diff(scandir($directoryPath),array('.','..'));
        foreach($files as $f){
            if(is_dir($directoryPath.'/'.$f))
                $l[]=$f;
        }
        return $l;
    }
    
    /**
     * array of files without directories... optionally filtered by extension
     * @param String $directoryPath
     * @param String $x extension (ex "xml")
     * @return Array
     */
    function file_list($directoryPath,$x=null){
        $l = array();
        $files = array_diff(scandir($directoryPath),array('.','..'));
        foreach($files as $f){
            if(is_file($directoryPath.'/'.$f)){
                if($x && strtolower(pathinfo($directoryPath.'/'.$f, PATHINFO_EXTENSION)) != $x){
                    continue;
                }
                $l[]=$f;
            }
        }
        return $l;
    }
}