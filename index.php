<?php

define('SF_INADMIN', false);
define('SF_INSITE', true);
define('SF_INCRON', false);

//date_default_timezone_set('Europe/Moscow');
date_default_timezone_set('Asia/Yekaterinburg');

// Одна сессия для всех поддоменов
if (SFConfig::$subdomainOneSession) {
    $baseDomain = implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), 1));
    session_set_cookie_params(0, '/', ".$baseDomain");
}

$_ENV['start'] = ['time' => microtime(true), 'memory' => memory_get_usage()];
session_start();

ini_set('default_charset', 'UTF-8');

include 'autoload.php';

include 'config.php';
include 'core/sflog.class.php';
include 'core/sfdb.class.php';
include 'core/sfuser.class.php';
include 'core/sfcore.class.php';
include 'core/sfpage.class.php';

SFDB::connect();

SFUser::login();
SFCore::init();
SFPage::init();

ob_start();
SFCore::execute();
$html = ob_get_clean();
$siteParams = SFCore::siteParam();
foreach ($siteParams as $paramAlias => $paramValue) {
    $html = str_replace('{' . $paramAlias . '}', $paramValue, $html);
}
$html = str_replace('{year}', date('Y'), $html);
preg_match_all("@\{position_([a-z_\-]+)\}@Ui", $html, $matches);
if (!empty($matches[1])) {
    foreach ($matches[1] as $pos) {
        ob_start();
        SFPage::position($pos);
        $ppp = ob_get_clean();
        $html = str_replace("{position_$pos}", $ppp, $html);
    }
}

echo sanitizeOutput($html);

function imDev() {
    if (isset($_GET['setimdev'])) {
        setcookie('imdev', 1, time() + 60 * 60 * 24 * 30, '/');
        header("location: ./");
        exit;
    }
    $ret = !empty($_COOKIE['imdev']);
    $ret |= $_SERVER['REMOTE_ADDR'] == '127.0.0.1';
    $ret |= strpos($_SERVER['REMOTE_ADDR'], '192.168') === 0;
    return $ret;
}

function barf($f, $l, $debug = false, $printTrace = false) {
    if (imDev()) {
        $d = & $_ENV['debug_barf'];
        $s = & $_ENV['debug_start'];
        empty($d) && $d = [];
        $di = [];
        $di['line'] = $l;
        $di['file'] = $f;
        $di['time'] = microtime(true) - $s['time'];
        $di['m0'] = round((memory_get_usage() - $s['m0']) / 1024);
        $di['m1'] = round((memory_get_usage(true) - $s['m1']) / 1024);
        $trace = '';
        if ($printTrace) {
            ob_start();
            debug_print_backtrace();
            $trace = nl2br(ob_get_clean());
        }
        $di['debug'] = $debug . $trace;
        $d[] = $di;
    }
}

function barfOut() {
    if (imDev()) {
        echo '<table cellpadding=3 border=1>';
        foreach ($_ENV['debug_barf'] as $row) {
            echo '<tr>';
            echo '<td>' . $row['file'] . '</td>';
            echo '<td>' . $row['line'] . '</td>';
            echo '<td>' . round($row['time'], 3) . '</td>';
            echo '<td>' . $row['m0'] . '</td>';
            echo '<td>' . $row['m1'] . '</td>';
            echo '<td>' . var_export($row['debug'], true) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        die;
    }
}

function sanitizeOutput($buffer) {
    if (imDev()) {
        return $buffer;
    }
    $search = array(
        '/\>[^\S ]+/s', // strip whitespaces after tags, except space
        '/[^\S ]+\</s', // strip whitespaces before tags, except space
        '/(\s)+/s', // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );
    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}
