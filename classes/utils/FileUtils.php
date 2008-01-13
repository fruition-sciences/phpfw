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
}