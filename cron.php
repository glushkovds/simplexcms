<?php

/**
 * Usage from CLI: php cron.php --cron_id={id}
 */

define('SF_INADMIN', false);
define('SF_INSITE', false);
define('SF_INCRON', true);
define('SF_INAPI', false);

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
include 'core/sflog.class.php';
include 'core/sfdb.class.php';
include 'core/sfuser.class.php';
include 'core/sfcore.class.php';
include 'core/sfpage.class.php';

SFDB::connect();

cliParamsToGET();

SFUser::login();
SFCore::init();

$cronId = (int) @$_GET['cron_id'];
$q = "
    SELECT *,
        (SELECT class FROM component WHERE component_id = cron.ext_id) class,
        (SELECT class FROM module WHERE module_id = cron.module_id) module_class 
    FROM cron 
    WHERE active = 1" . ($cronId ? " AND id = $cronId" : '') . "
";
$jobs = SFDB::assoc($q);
foreach ($jobs as $job) {
    if (!$cronId && !timingPassed($job['timing'])) {
        continue;
    }
    $action = $job['action'];
    $params = $job['cparams'];
    ob_start();
    if ($job['ext_id']) {
        $ext = strtolower(str_replace('Com', '', $job['class']));
        $file = dirname(__FILE__) . "/ext/$ext/cron$ext.class.php";
        if (is_file($file)) {
            include_once $file;
            $class = "Cron" . ucfirst($ext);
            if (method_exists($class, $action)) {
                $class::$action($params);
            }
        }
    }
    if ($job['module_id']) {
        $class = $job['module_class'];
        include_once 'autoload.php';
        if (method_exists($class, $action)) {
            $class::$action($params);
        }
    }
    if ($job['plugin_name']) {
        $plugin = $job['plugin_name'];
        $dir = strtolower(substr($plugin, 4));
        $file = dirname(__FILE__) . "/plug/$dir/" . strtolower($plugin) . ".class.php";
        if (is_file($file)) {
            include_once $file;
        }
        $plugin::$action($params);
    }
    $result = str_replace("'", "\'", ob_get_clean());
    $q = "insert into cron_log set cron_id = {$job['id']}, datetime = now(), result = '$result'";
    SFDB::query($q);
}

if (timingPassed('30 0 * * *')) {
    $q = "delete from cron_log where adddate(datetime,interval 2 month) < now()";
    SFDB::query($q);
}

function cliParamsToGET() {
    global $argc, $argv;
    if ($argc == 1) {
        return;
    }
    foreach (array_slice($argv, 1) as $arg) {
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

function timingPassed($timing) {
    # m h dom mon dow
    #$timing = "* 11-18 * * 1";
    $now = (int) date("i") . ' ' . (int) date("H") . ' ' . (int) date("d") . ' ' . (int) date("m") . ' ' . (int) date("w");
    $cronTime = explode(' ', $timing);

    $parts = array('i', 'H', 'd', 'm', 'w');
    foreach ($parts as $index => $dateParam) {
        $val = str_replace('*', (int) date($dateParam), $cronTime[$index]);
        $del = 0;
        if (strpos($val, '/') !== false) {
            $tmp = explode('/', $val);
            $val = $tmp[0];
            $del = $tmp[1];
        }

        $vals = explode(',', $val);
        $passed = false;
        $cur = (int) date($dateParam);
        foreach ($vals as $val) {
            if (strpos($val, '-') !== false) {
                $tmp = explode('-', $val);
                if ($tmp[0] <= $cur && $cur <= $tmp[1]) {
                    $val = $cur;
                }
            }

            $passed = (int) $val === $cur && (!$del || $del && $val % $del === 0);
            if ($passed) {
                break;
            }
        }
        $cronTime[$index] = $passed ? $cur : 'false';
    }

    $cronTimeStr = implode(' ', $cronTime);
    return $now == $cronTimeStr;
}
