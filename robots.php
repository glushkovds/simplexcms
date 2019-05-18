<?php

include 'config.php';
include 'core/sfdb.class.php';

SFDB::connect();

$q = "SELECT * FROM settings WHERE alias = 'robots.txt'";
$row = SFDB::result($q);
$tmp = explode('.', $_SERVER['HTTP_HOST']);
$subdomain = $tmp[0];
$text = str_replace('{subdomain}', $subdomain, $row['value']);

header("Content-Type: text/plain");
echo $text;


