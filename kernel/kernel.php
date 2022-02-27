<?php
    if(!session_id()) session_start();

    if(!defined("ACCESS_FROM_SITE")){
        exit("");
    }

    ini_set('display_startup_errors', 1);
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    define('MAIN_PATH', dirname(__DIR__).'\\');
    define('SITE_DOMAIN', 'http://'.$_SERVER['SERVER_NAME']);

    const STYLE_NAME    = 'Default';
    const STYLE         = MAIN_PATH . 'templates\\' . STYLE_NAME . '\\';
    const STYLE_URL     = SITE_DOMAIN . '/templates/' . STYLE_NAME . '/';

    require_once("classes/kernel.class.php");
    require_once("classes/safemysql.class.php");
    require_once("classes/db_interface.class.php");
    require_once("classes/template.class.php");
    require_once("classes/notification.class.php");
    require_once("classes/section.class.php");
    require_once("classes/element.class.php");
    require_once("classes/catalog.class.php");

    define('FORM_TOKEN', Kernel::CreateAntiCSRFToken());

    $DBHost  = "127.0.0.1";
    $DBLogin = "root";
    $DBPass  = "root123";
    $DBName  = "catalog";

    Kernel::SetDBInterface(new DB_Interface($DBHost, $DBLogin, $DBPass, $DBName));

