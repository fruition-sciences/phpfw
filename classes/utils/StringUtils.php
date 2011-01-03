<?php
/*
 * Created on Jul 21, 2007
 * Author: Yoni Rosenbaum
 *
 */

class StringUtils {
    /**
     * Construct a string containing the elements of the given array, dilimited
     * by the given delimiter.
     * Note: The php function implode can be used instead.
     *
     * @param array $a array
     * @param String $delimiter the delimiter to use between each 2 items
     * @param boolean $delimiterAtEnd if true, a delimiter will be placed at end.
     */
    public static function arrayToString($a, $delimiter, $delimiterAtEnd=false) {
        $str = "";
        $len = sizeof($a);
        for ($i=0; $i<$len; $i++) {
            $str .= $a[$i];
            if ($delimiterAtEnd || $i < $len-1) {
                $str .= $delimiter;
            }
        }
        return $str;
    }

    /**
     * Join array elements with a string. Ignores null elements.
     *
     * @param String $glue the delimiter
     * @param Array $pieces the array
     */
    public static function implodeIgnoreNull($glue, $pieces) {
        $newArray = array();
        foreach ($pieces as $item) {
            if ($item !== null) {
                $newArray[] = $item;
            }
        }
        return implode($glue, $newArray);
    }

    /**
     * Truncate the given file path (for presentation purposes) so that it's no
     * longer than the given length.
     * Current implementation truncates the beginning of the path, replacing it
     * with three dots.
     *
     * @param String $filePath the file path
     * @param int $len the maximum length allowed
     * @return String the truncated path.
     */
    public static function truncateFilePath($filePath, $len) {
        if (!$filePath) {
            return "";
        }
        if (strlen($filePath) < $len) {
            return $filePath;
        }
        $delimiter = self::getPathDelimiter($filePath);
        $ellipses = "..." . $delimiter;
        $permittedLength = $len - strlen($ellipses);
        $tokens = explode($delimiter, $filePath);
        $newPath = "";
        $i=sizeof($tokens)-1;
        // Reconstruct the path from the end, token by token.
        while ($i>=0 && strlen($newPath) <= $permittedLength) {
            $proposedNewPath = $newPath;
            if ($proposedNewPath != "") {
                $proposedNewPath = $delimiter . $proposedNewPath;
            }
            $proposedNewPath = $tokens[$i] . $proposedNewPath;
            if (strlen($proposedNewPath) <= $permittedLength) {
                // Adding the token did not exceed the limit
                $newPath = $proposedNewPath;
            }
            else {
                // Adding the token exceeded the limit
                break;
            }
            $i--;
        }

        return $ellipses . $newPath;
    }

    /**
     * Find the path delimiter used in the given file path.
     *
     * @param String $filePath the file path to examine.
     */
    private static function getPathDelimiter($filePath) {
        if (stristr($filePath, '\\')) {
            return '\\';
    	}
        return "/";
    }

    /**
     * Format a number of bytes into a human readable format.
     * Optionally choose the output format and/or force a particular unit
     *
     * @param   int     $bytes      The number of bytes to format. Must be positive
     * @param   string  $format     Optional. The output format for the string
     * @param   string  $force      Optional. Force a certain unit. B|KB|MB|GB|TB
     * @return  string              The formatted file size
     */
    public static function formatFileSize($bytes, $format = '', $force = '')
    {
        $force = strtoupper($force);
        $defaultFormat = '%01d %s';
        if (strlen($format) == 0)
            $format = $defaultFormat;

        $bytes = max(0, (int) $bytes);

        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

        $power = array_search($force, $units);

        if ($power === false)
            $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return sprintf($format, $bytes / pow(1024, $power), $units[$power]);
    }

    /**
     * @param String $name
     * @deprecated
     */
    public static function capitalizeFirstLetter($name) {
        return ucfirst($name);
    }

    /**
     * Tests if a text starts with an given string.
     *
     * @param String $haystack text to search within
     * @param String $needle string to look for
     * @return bool
     */
    public static function startsWith($haystack, $needle){
        return strpos($haystack, $needle) === 0;
    }
}