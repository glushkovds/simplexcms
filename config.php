<?php

class SFConfig {

    public static $db_type = 'mysql';
    public static $db_host = 'localhost';
    public static $db_user = 'root';
    public static $db_pass = 'root';
    public static $db_name = 'simplex';
    public static $component_default = 'ComContent';
    public static $theme = 'default';
    
    public static $subdomainOneSession = false;
    
    /**
     * @see /core/sflog.class.php
     */
    public static $logLevel = 'debug';
    public static $logPath = '/var/log';

}

if (!function_exists('imDev')) {

    function imDev() {
        return !empty($_COOKIE['imdev']);
    }

}

