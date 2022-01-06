<?php
    define('ROOTDIR',str_replace('\\','/',__DIR__).'/');
    $basedir = explode('/',$_SERVER['REQUEST_URI'])[1];
    define(
        'HOMEURI',
        $_SERVER['REQUEST_SCHEME'] .'://'. 
        $_SERVER['SERVER_NAME'].':' .
        $_SERVER['SERVER_PORT'] . '/' .
        $basedir . '/' . preg_replace('#.*/'.$basedir.'/#','',ROOTDIR)
    );

    //database details
    //specify server name
    define('SERVER', $_SERVER['SERVER_NAME']);
    //specify database name
    define('DATABASE','');
    //Enter db username
    define('DBUSER','root');
    //Enter db password
    define('DBPASS','');
?>