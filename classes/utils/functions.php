<?php

function absInclude($file) {
  $path =  getAppDir() . $file;
  include $path;
}

function getAppDir() {
    $script = $_SERVER["SCRIPT_FILENAME"];
    return getDir($script);
}

/**
 * Get the path of the directory of the given file. Includes '/' at the end.
 * @param string $file the file
 */
function getDir($file) {
  return preg_replace('/([^\/]+)$/', "", $file);
}

function beginsWith( $str, $sub ) {
   return ( substr( $str, 0, strlen( $sub ) ) === $sub );
}

function endsWith( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

function arrayToString($a, $delimiter) {
    $started = false;
    $str = "";
    foreach ($a as $item) {
        if ($started) {
            $str .= $delimiter;
        }
        $str .= $item;
        $started = true;
    }
    return $str;
}

function log_pre($message) {
    echo "<pre>";
    var_dump($message);
    echo "</pre>";
}

?>
