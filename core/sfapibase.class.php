<?php

class SFApiBase
{

    public function execute()
    {
        $this->tryAuth();
        $method = 'action' . ucfirst(SFCore::uri(0) ?: 'index');
        if ($method && method_exists($this, $method)) {
            return $this->$method($_GET);
        }
        header("HTTP/1.0 404 Not Found");
    }

    public static function tryAuth()
    {
        static::tryAuthBasic();
    }

    protected static function tryAuthBasic()
    {
        if ($login =& $_SERVER['PHP_AUTH_USER']) {
            $pass = $_SERVER['PHP_AUTH_PW'] ?? '';
            SFUser::authorizeOnce($login, $pass);
        }
    }

}
