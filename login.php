<?php

define('SF_INADMIN', false);
define('SF_INSITE', true);
define('SF_INCRON', false);
define('SF_INAPI', false);

$timeStart = microtime();
session_start();
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

include 'config.php';
include 'core/sflog.class.php';
include 'core/sfdb.class.php';
SFDB::connect();

if (isset($_REQUEST['login']['login']) && isset($_REQUEST['login']['password'])) {
    if (preg_match('@^[0-9a-z\@\-\.]+$@', $_REQUEST['login']['login'])) {
        SFDB::bind(array('USER_LOGIN' => $_REQUEST['login']['login']));
        $q = "SELECT u.user_id, u.role_id, u.login, u.password
        FROM user u
        JOIN user_role r ON r.role_id=u.role_id
        WHERE login=@USER_LOGIN
          AND u.active=1
          AND r.active=1";
        if ($row = SFDB::result($q)) {
            if (md5($_REQUEST['login']['password']) === $row['password']) {
                $hash = md5(rand(0, 999) . microtime());
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_hash'] = $hash;

                SFDB::bind(array('USER_ID' => $row['user_id'], 'USER_HASH' => $hash));
                $q = "UPDATE user SET hash=@USER_HASH WHERE user_id=@USER_ID";
                SFDB::query($q);

                if (isset($_POST['login']['remember'])) {
                    setcookie("ch", md5($row['user_id']), time() + 60 * 60 * 24 * 3, "/");
                    setcookie("cs", $hash, time() + 60 * 60 * 24 * 3, "/");
                }
            }
        }
    }
}
header('location: ' . (empty($_REQUEST['r']) ? '/' : $_REQUEST['r']));
