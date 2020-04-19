<?php

define('SF_INADMIN', true);
define('SF_INSITE', false);
define('SF_INCRON', false);

$m0 = memory_get_usage();
$time_start = microtime();
session_start();

//date_default_timezone_set('Europe/Moscow');
date_default_timezone_set('Asia/Yekaterinburg');

ini_set('default_charset', 'UTF-8');

include '../autoload.php';
include 'autoload.php';

include '../config.php';
include '../core/sflog.class.php';
include '../core/sfcore.class.php';
include '../core/sfpage.class.php';
include '../core/sfdb.class.php';
include '../core/sfuser.class.php';
include '../core/sffile.class.php';
include '../core/sfservice.class.php';
include '../core/sfextbase.class.php';
include '../core/sfcombase.class.php';
include '../core/sfmodbase.class.php';
include '../core/sfbuffer.class.php';
include 'base/base.class.php';
include 'base/struct.class.php';
include 'core/sfadmincore.class.php';
include 'core/sfadminpage.class.php';



SFDB::connect();
SFUser::login('admin');
SFAdminCore::init();
SFAdminCore::execute();
