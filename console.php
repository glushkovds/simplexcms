<?php

ini_set('display_errors', 1);
define('SF_INADMIN', false);
define('SF_INSITE', false);
define('SF_INCRON', true);

ini_set('max_execution_time', 600);
ini_set('iconv.internal_encoding', 'UTF-8');
ini_set('mbstring.internal_encoding', 'UTF-8');

if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'www.' . basename(__FILE__);
}
if (!isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);
}

include 'autoload.php';

include 'config.php';
include 'core/sfdb.class.php';
include 'core/sfuser.class.php';
include 'core/sfcore.class.php';
include 'core/sfpage.class.php';
include 'core/sfbuffer.class.php';

SFDB::connect();

cliParamsToGET();

SFUser::login();
SFCore::init();

$job = explode('/', @$argv[1]);
if (count($job) !== 2) {
    echo "Error!! example usage: php console.php ext/action\nOR: ./sf ext/action\n";
    exit;
}
$ext = $job[0];
$action = 'action' . ucfirst($job[1]);
$file = $_SERVER['DOCUMENT_ROOT'] . "/ext/$ext/console$ext.class.php";
if (is_file($file)) {
    include_once $file;
    $class = "Console" . ucfirst($ext);
    if (class_exists($class)) {
        $handler = new $class();
        if (method_exists($handler, $action)) {
            $handler->$action();
            exit;
        } else {
            echo "method $action not found\n";
        }
    } else {
        echo "class $class not found\n";
    }
} else {
    echo "extension $ext not found\n";
}

function cliParamsToGET() {
    global $argc, $argv;
    if ($argc == 1) {
        return;
    }
    foreach (array_slice($argv, 2) as $arg) {
        if (strpos($arg, '--') !== 0) {
            echo 'error reading args';
            die;
        }
        $tmp = explode('=', trim($arg, '-'));
        $key = $tmp[0];
        $value = $tmp[1];
        $_GET[$key] = $value;
    }
}
