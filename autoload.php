<?php

spl_autoload_register(function ($className) {

    $appRootDir = __DIR__;

    $include = function($file) {
        if (is_file($file)) {
            include_once $file;
            return true;
        }
        return false;
    };

    $explodeCamelCase = function($str) {
        $matches = [];
        preg_match_all('/((?:^|[A-Z])[a-z\d]+)/', $str, $matches);
        return $matches[0];
    };

    if ('SF' === substr($className, 0, 2)) {
        if ($include("$appRootDir/core/" . strtolower($className) . '.class.php')) {
            return;
        }
    }

    if ('Com' === substr($className, 0, 3)) {
        $path = "$appRootDir/ext/" . substr($className, 3) . '/';
        if ($include(strtolower($path . $className) . '.class.php')) {
            return;
        }
    }

    if ('Model' === substr($className, 0, 5)) {
        $parts = array_map('strtolower', $explodeCamelCase($className));
        $dir = $parts[1];
        for ($i = 1; $i < count($parts); $i++) {
            $dir = implode('', array_slice($parts, 1, $i));
            $path = __DIR__ . '/ext/' . $dir . '/models';
            if (is_dir($path)) {
                if ($include($path . '/' . strtolower($className) . '.class.php')) {
                    return;
                }
            }
        }
        $path = "$appRootDir/models/";
        if ($include(strtolower($path . $className) . '.class.php')) {
            return;
        }
    }

    if ('Mod' === substr($className, 0, 3)) {
        $dir = substr($className, 3);
        $a = [];
        if (preg_match('@^([A-Z]+[^A-Z]+)[A-Z]+@', $dir, $a)) {
            $dir = $a[1];
        }
        $path = "$appRootDir/ext/$dir/";
        if ($include(strtolower($path . $className) . '.class.php')) {
            return;
        }
    }

    if ('Plug' === substr($className, 0, 4)) {
        $path = "$appRootDir/plug/" . substr($className, 4) . '/';
        if ($include(strtolower($path . $className) . '.class.php')) {
            return;
        }
    }
});
