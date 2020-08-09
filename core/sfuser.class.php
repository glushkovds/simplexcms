<?php

class SFUser
{
    public static $id = 0;
    public static $login = '';
    public static $role_id = 0;
    public static $role_name = '';

    /**
     *
     * @var ZSFUserInstance
     */
    protected static $instance;

    public static function login($type = 'site')
    {
//        if (!isset(self::$instance)) {
        if ('site' == $type) {
            self::$instance = new ZSFUserInstance('user_id', 'user_hash', 'hash', 'ch', 'cs');
        }
        if ('admin' == $type) {
            self::$instance = new ZSFUserInstance('admin_user_id', 'admin_user_hash', 'hash_admin', 'cha', 'csa');
        }
//        }
        self::$id = self::$instance->id;
        self::$login = self::$instance->login;
        self::$role_id = self::$instance->role_id;
        self::$role_name = self::$instance->role_name;
    }

    public static function privIds()
    {
        return self::$instance->privIds();
    }

    public static function privNames()
    {
        return self::$instance->privNames();
    }

    /**
     *
     * @param string|int|bool $priv - можно указать название привилегии, можно ее ID. Если false, то вернет все привилегии
     * @return bool|array
     */
    public static function ican($priv = false)
    {
        return self::$instance->ican($priv);
    }

    /**
     * Возвращает информацию о пользователе
     * @param string (optional) $field - если указано, возвращает конкретное поле
     * @return array
     */
    public static function info($field = false)
    {
        return self::$instance->info($field);
    }

    public static function create($data)
    {
        if (self::$id) {
            return array('success' => false, 'error_code' => 1, 'error' => 'Пользователь уже авторизован');
        }

        $login = PlugPhone::extract((string)@$data['login']);
        $pass = (string)@$data['pass'] ?: rand(100000, 999999);
        $email = (string)@$data['email'];
        $name = (string)@$data['name'];

        $errors = array();
        $login ? null : $errors[] = 'логин';
        $email ? null : $errors[] = 'email';
        $name ? null : $errors[] = 'имя';
        if (count($errors)) {
            return array('success' => false, 'error_code' => 2, 'error' => 'Не указано: ' . implode(', ', $errors));
        }

        $q = "SELECT user_id FROM user WHERE login = '$login'";
        $user = SFDB::result($q, 0);
        if ($user) {
            return array('success' => false, 'error_code' => 3, 'error' => 'Пользователь с таким логином уже зарегистрирован');
        }

        $q = "SELECT user_id FROM user WHERE email = '$email'";
        $user = SFDB::result($q, 0);
        if ($user) {
            return array('success' => false, 'error_code' => 4, 'error' => 'Пользователь с таким почтовым адресом уже зарегистрирован');
        }

        $passMD5 = md5($pass);
        $hash = md5(microtime());
        $set = array("login = '$login', email = '$email', name = '$name', password = '$passMD5', hash = '$hash'");
        $q = "INSERT INTO user SET " . implode(', ', $set);
        SFDB::query($q);
        $userId = SFDB::insertID();

        if (!$userId) {
            return array('success' => false, 'error_code' => 5, 'error' => 'Ошибка регистрации');
        }

        $HTTPHost = PlugPunyCode::httpHost();

        // Отправляем письмо об успешной регистрации
        if ($email) {
            ob_start();
            include $_SERVER['DOCUMENT_ROOT'] . '/theme/default/mail/user_create.tpl';
            $html = ob_get_contents();
            ob_end_clean();
            PlugMail::staticSend($email, "Регистрация на сайте $HTTPHost", $html);
        }

        // Отправляем СМС с рег. данными
        $smsText = "Для Вас зарегистрирован аккаунт на сайте $HTTPHost: логин $login, пароль $pass";
        PlugSMS::send($login, $smsText);

        if ($userId) {
            setcookie("user_id", $userId, time() + 3600 * 24 * 365, '/');
            setcookie("user_hash", $hash, time() + 3600 * 24 * 365, '/');
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_hash'] = $hash;
            self::login();
        }

        return array('success' => true, 'user_id' => $userId);
    }

    public static function authorizeOnce($login, $password)
    {
        $GLOBALS[self::class]['login'] = $login;
        $GLOBALS[self::class]['password'] = $password;
        static::login();
    }

}

class ZSFUserInstance
{

    public $id = 0;
    public $login = '';
    public $role_id = 0;
    public $role_name = '';
    private $priv_ids = array();
    private $priv_names = array();
    private $info;
    private $idName;
    private $hashName;
    private $dbHashName;
    private $remIdName;
    private $remHashName;

    /**
     *
     * @param type $idName
     * @param type $hashName
     * @param type $dbHashName
     * @param type $remIdName
     * @param type $remHashName
     */
    public function __construct($idName, $hashName, $dbHashName, $remIdName, $remHashName)
    {
        $this->idName = $idName;
        $this->hashName = $hashName;
        $this->dbHashName = $dbHashName;
        $this->remIdName = $remIdName;
        $this->remHashName = $remHashName;
        $this->login();
    }

    private function login()
    {
        if (isset($_REQUEST['logout'])) {
            $_SESSION[$this->idName] = 0;
            $_SESSION[$this->hashName] = '';
            setcookie($this->remIdName);
            setcookie($this->remHashName);
            unset($_COOKIE[$this->remIdName]);
            unset($_COOKIE[$this->remHashName]);
        } else {

            if (!isset($_SESSION[$this->idName]) && isset($_COOKIE[$this->remIdName]) && isset($_COOKIE[$this->remHashName])) {
                $userId_md5 = SFDB::escape($_COOKIE[$this->remIdName]);
                $hash = SFDB::escape($_COOKIE[$this->remHashName]);
                $q = "select user_id `id` from `user` where md5(`user_id`) = '$userId_md5' and $this->dbHashName = '$hash'";
                if ($userId = SFDB::result($q, "id")) {
                    $_SESSION[$this->idName] = $userId;
                    $_SESSION[$this->hashName] = $hash;
                    setcookie($this->remIdName, $_COOKIE[$this->remIdName], time() + 60 * 60 * 24 * 3, "/");
                    setcookie($this->remHashName, $_COOKIE[$this->remHashName], time() + 60 * 60 * 24 * 3, "/");
                }
            }

            // Authorization for API, for authorize method
            if (!empty($GLOBALS[self::class]['login']) && !empty($GLOBALS[self::class]['password'])) {
                $q = "
                    SELECT user_id, role_id, login, $this->dbHashName, r.name role_name
                    FROM user u
                    JOIN user_role r USING(role_id)
                    WHERE login='" . SFDB::escape($GLOBALS[self::class]['login']) . "' AND u.active=1 AND r.active=1
                ";
                $r = SFDB::query($q);
                if ($row = SFDB::fetch($r)) {
                    if (md5($GLOBALS[self::class]['password']) === $row['password']) {
                        $this->id = (int)$row[$this->idName];
                        $this->login = $row['login'];
                        $this->role_id = (int)$row['role_id'];
                        $this->role_name = $row['role_name'];
                    }
                }
            }

            if (!empty($_SESSION[$this->idName]) && !empty($_SESSION[$this->hashName])) {
                $q = "
                    SELECT user_id, role_id, login, $this->dbHashName, r.name role_name
                    FROM user u
                    JOIN user_role r USING(role_id)
                    WHERE user_id=" . (int)$_SESSION[$this->idName] . " AND u.active=1 AND r.active=1
                ";
                $r = SFDB::query($q);
                if ($row = SFDB::fetch($r)) {
                    if ($_SESSION[$this->hashName] === $row[$this->dbHashName]) {
                        $this->id = (int)$_SESSION[$this->idName];
                        $this->login = $row['login'];
                        $this->role_id = (int)$row['role_id'];
                        $this->role_name = $row['role_name'];
                    }
                }
            }
        }

        $q = "
            SELECT priv_id, name
            FROM user_priv
            WHERE active=1
            AND (
                priv_id IN(SELECT priv_id FROM user_role_priv WHERE role_id" . ($this->role_id ? '=' . (int)$this->role_id : " IS NULL") . ")
                OR priv_id IN(SELECT priv_id FROM user_priv_personal WHERE user_id=" . $this->id . ")
            )
        ";
        $r = SFDB::query($q);
        while ($row = SFDB::fetch($r)) {
            $this->priv_ids[(int)$row['priv_id']] = (int)$row['priv_id'];
            $this->priv_names[$row['name']] = $row['name'];
        }
    }

    /**
     * Возвращает информацию о пользователе
     * @param string (optional) $field - если указано, возвращает конкретное поле
     * @return array
     */
    public function info($field = false)
    {
        if (!$this->id) {
            return false;
        }
        if (!isset($this->info)) {
            $q = "select * from user where user_id = " . $this->id;
            $this->info = SFDB::result($q);
            $this->info['role_name'] = $this->role_name;
        }
        $ret = false;
        if ($field === false) {
            $ret = $this->info;
        } elseif (isset($this->info[$field])) {
            $ret = $this->info[$field];
        }
        return $ret;
    }

    public function privIds()
    {
        return count($this->priv_ids) ? $this->priv_ids : array(0);
    }

    public function privNames()
    {
        return count($this->priv_names) ? $this->priv_names : array('');
    }

    public function ican($priv)
    {
        if (false === $priv) {
            return $this->priv_names;
        }
        if (is_int($priv)) {
            return isset($this->priv_ids[$priv]);
        }
        return isset($this->priv_names[$priv]);
    }

}
