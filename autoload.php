<?php

spl_autoload_register(function ($className) {
    $path = 'core/';
    switch (substr($className, 0, 3)) {
        case 'Com' :
            $path = 'ext/' . substr($className, 3) . '/';
            break;
        case 'Mod' :
            if (substr($className, 0, 5) === 'Model') {
                $dir = substr($className, 5);
                $path = 'ext/' . $dir . '/';
            } else {
                $dir = substr($className, 3);
                if (preg_match('@^([A-Z]+[^A-Z]+)[A-Z]+@', $dir, $a)) {
                    $dir = $a[1];
                }
                $path = 'ext/' . $dir . '/';
            }
            break;
        case 'Plu' :
            $path = 'plug/' . substr($className, 4) . '/';
            break;
    }
    $fileName = strtolower($path . $className) . '.class.php';
    if (is_file($fileName)) {
        include_once $fileName;
    }
});
