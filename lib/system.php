<?php
session_start();
if (DEBUG) {
    $_SESSION['timer'] = time();
}
require_once './etc/settings.php';


function __autoload($strClassName) {
    try {
        $strClassFile = settings::get('path_lib').$strClassName.DIRECTORY_SEPARATOR.$strClassName.'.php';
        if (!file_exists($strClassFile)) {
            throw new exception('Class '.$strClassName. ' not found!');
        } else {
            require_once $strClassFile;
        }

    } catch (exception $e) {
        echo $e->getMessage();
    }
}

class settings {
    static function get($strName) {
        return ((isset($_SESSION['settings'][$strName]) ? $_SESSION['settings'][$strName] : NULL));
    }
}

/**
 * Description of system
 *
 * @author Jens
 */
class system {



}
?>
