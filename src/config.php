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

    define('DEBUGGING',true);

    //database details
    //specify server name
    $SERVER = $_SERVER['SERVER_NAME'];
    //specify database name
    $DATABASE = 'bbvs';
    //Enter db username
    $DBUSER = 'root';
    //Enter db password
    $DBPASS = '';

    //mail
    $MAILHOST = "";
    $MAILUSERNAME = "";
    $MAILPASSWORD = "";

    //salt
    $SALT = 'fqubf3bf9021fcaocer13rf3298bf4nfw498nf34';

?>