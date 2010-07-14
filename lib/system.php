<?php
/**
 * start the session
 */
session_start();
/**
 * include the global settings
 */
require_once './etc/settings.php';
/**
 * start timer if DEBUG is true
 */
if (DEBUG) {
    $_SESSION['timer'] = time();
}


/**
 * magic autoloader
 *
 * @access public
 * @param string $strClassName name of class to autoload
 */
function __autoload($strClassName) {
    try {
        //if (strpos('_',$strClassName!==false)) {
        //    $arrClass     = explode('_',$strClassName);
        //    $classFolder  = $arrClass[0];
        //    $className    = $strClassName;
        //} else {
            $className = $classFolder = $strClassName;
        //}
        $strClassFile = settings::get('path_lib').$classFolder.DIRECTORY_SEPARATOR.$className.'.php';
        if (!file_exists($strClassFile)) {
            throw new exception('Class '.$strClassName. ' not found!');
        } else {
            require_once $strClassFile;
        }
    } catch (exception $e) {
        echo $e->getMessage();
    }
}

/**
 * settings class
 */
class settings {
    /**
     * returns the value of the given settings-variable
     *
     * @param string $strName name of settings-variable
     * @return string
     * @access static
     * @access public
     */
    static function get($strName) {
        return ((isset($_SESSION['settings'][$strName]) ? $_SESSION['settings'][$strName] : NULL));
    }
}

/**
 * system class
 */
class system {

    /**
     * 
     */
    static function cleanupID($strOrig) {
        return preg_replace ("/([^A-Za-z0-9\+_\-,]+)/", "", $strOrig);
    }

    
}
?>
