<?php

spl_autoload_register(function ($className) {
    $classNameRaw = $className;
    $className = strtolower($className);
    $path = 'core/';
    $fname = substr($className, 8);
    if (strpos($className, 'admin') === 0) {
//        echo $className . '<br>';
        switch (substr($className, 5, 3)) {
            case 'com' :
                $path = 'com/' . substr($className, 3) . '/';
                break;
            case 'mod' :
                $path = 'mod/' . substr($className, 8) . '/';
                break;
            case 'plu' :
                $path = 'plug/' . substr($className, 9) . '/';
                $fname = substr($className, 9);
                break;
            case 'wid' :
                include_once 'core/sfadminwidgetbase.class.php';
                $matches = array();
                preg_match_all("@[A-Z][a-z]+@", $classNameRaw, $matches);
                $extname = $matches[0][2];
                $path = "{$_SERVER['DOCUMENT_ROOT']}/ext/$extname/admin/widget/";
                $fname = implode('', array_slice($matches[0], 3));
                break;
            case 'not' :
                $fname = substr($className, 11);
                $path = "{$_SERVER['DOCUMENT_ROOT']}/ext/$fname/admin/notify.class.php";
                if (is_file($path)) {
                    include_once $path;
                    return;
                }
                break;
        }

        $fileName = strtolower($path . $fname) . '.class.php';
        if (is_file($fileName)) {
            include_once $fileName;
        }
    } elseif (strpos($className, 'sfadminnotify') === 0) {
        $path = 'notify/';
        $fname = substr($className, 13);
        $fileName = strtolower($path . $fname) . '.class.php';
        if (is_file($fileName)) {
            include_once $fileName;
        }
    } elseif (substr($className, 0, 3) === 'sff') {
        if (in_array($className, ['sffield', 'sfforeignkey'])) {
            $className = substr($className, 2);
        } else {
            $className = substr($className, 3);
        }
        $path = '../core/fields/';
        $fileName = strtolower($path . $className) . '.class.php';
        if (is_file($fileName)) {
            include_once $fileName;
        }
    }
});
