<?php

define('SF_INADMIN', false);
define('SF_INSITE', false);
define('SF_INCRON', false);
define('SF_INAPI', true);

$m0 = memory_get_usage();

date_default_timezone_set('Asia/Yekaterinburg');

$time_start = microtime();
session_start();

ini_set('iconv.internal_encoding', 'UTF-8');
ini_set('mbstring.internal_encoding', 'UTF-8');
setlocale(LC_ALL, 'ru_RU.utf8');
setlocale(LC_NUMERIC, 'en_US.UTF-8');


include 'autoload.php';
PlugLiteFrame::init();

include 'config.php';
include 'core/sfdb.class.php';
include 'core/sfuser.class.php';
include 'core/sfcore.class.php';
include 'core/sfapibase.class.php';
include 'core/sfapijson.class.php';
include 'core/sfapiresponse.class.php';

SFDB::connect();

SFApiBase::tryAuth();
SFCore::init();

$api = SFCore::getComponentAPI();
//$api = new APILogin();
if ($api) {
    $api->execute();
} else {
    header("HTTP/1.0 404 Not Found");
    echo '404 Not Found';
}

