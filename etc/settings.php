<?php

define('TAB'    ,"\t");
define('CRLF'   ,"\n");
define('DEBUG'  ,true);

$strInstallPath = str_replace('etc','',dirname(__FILE__)).'';

$_SETTINGS = array (

    'system_title'      => 'All the small things',
    'system_version'    => '0.00.1',

    'path_etc'          => $strInstallPath.'etc'.DIRECTORY_SEPARATOR,
    'path_lib'          => $strInstallPath.'lib'.DIRECTORY_SEPARATOR,
    'path_share'        => $strInstallPath.'share'.DIRECTORY_SEPARATOR,
    'path_css'          => $strInstallPath.'share'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR,
    'path_js'           => $strInstallPath.'share'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR,

    'web_css'           => './share/css/',
    'web_js'            => './share/js/',
);

$_SESSION['settings'] = $_SETTINGS;

?>
