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
    public static function dirList($directoryPath){
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
    public static function fileList($directoryPath,$x=null){
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
    
    /**
     * Put unit at an integer and return them in an array (ex: 45678 == 45,67KB)
     * @param Int $bytes integer in bytes
     * @param Int $precision is the number of decimal digits to round to
     * @return Array [value, unit]
     */
	public static function convertBytes($bytes, $precision = 2) {
    	$units = array('B', 'KB', 'MB', 'GB', 'TB');
  
    	$bytes = max($bytes, 0);
    	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    	$pow = min($pow, count($units) - 1);
  
    	$bytes /= pow(1024, $pow);
    	
    	return array(round($bytes, $precision), $units[$pow]);
  		
    	//return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
	/**
	 * Check if the file exists in include paths or in current directory.
	 * string $filename The file name to lookup
	 * @param string $filename
	 * @return bool
	 */
	public static function existsInIncludePath($filename) {
	    $paths = explode(PATH_SEPARATOR, get_include_path());
	    foreach ($paths as $path) {
	        if (file_exists($path .'/'. $filename)) {
	            return true;
	        }
	    }
	    return file_exists($filename);
	}
}