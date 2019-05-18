<?php

class PlugMail {

    const MAIL_TRANSPORT_DAEMON = 1;
    const MAIL_TRANSPORT_SMTP = 2;
    const MAIL_TRANSPORT_FAKE_SUCCESS = 3;
    const MAIL_TRANSPORT_FAKE_ERROR = 4;

    private static $connect = array('host' => 'smtp.yandex.ru', 'port' => 465, 'fromru' => 'Mail');
    private $data = array(
        'to_email' => '', 'to_name' => '', 'from_name' => '', 'from_email' => '', 'as_plain' => false,
        'reply_email' => '', 'reply_name' => '', 'text' => '', 'subject' => '', 'can_answer' => false,
        'transport' => self::MAIL_TRANSPORT_DAEMON
    );

    /**
     * 
     * @param string $toEmail
     * @param string $subject
     * @param string $msg
     */
    public function __construct($toEmail, $subject, $msg) {
        $this->data['from_name'] = SFCore::siteParam('site_name');
        $this->data['from_email'] = "noreply@{$_SERVER['HTTP_HOST']}";

        $this->data['to_email'] = $toEmail;
        $this->data['subject'] = $subject;
        $this->data['text'] = $msg;

        if (!empty(SFCore::siteParam('send_deny_email'))) {
            $this->data['transport'] = self::MAIL_TRANSPORT_FAKE_SUCCESS;
        }
    }

    /**
     * Создает объект PlugMail
     * @param string $toEmail
     * @param string $subject
     * @param string $msg
     * @return PlugMail
     */
    public static function create($toEmail, $subject, $msg) {
        $instance = new static($toEmail, $subject, $msg);
        return $instance;
    }

    public function setToName($name) {
        $this->data['to_name'] = trim($name);
        return $this;
    }

    public function setTransport($transport) {
        $this->data['transport'] = $transport;
        return $this;
    }

    /**
     * 
     * @param type $toEmail
     * @param type $subject
     * @param type $msg
     * @return bool
     */
    public static function staticSend($toEmail, $subject, $msg) {
        $instance = new static($toEmail, $subject, $msg);
        return $instance->send();
    }

    /**
     * 
     * @param string $str Допускается: Иванов Иван или ivanov@mail.ru или Иванов Иван <ivanov@mail.ru>
     * @param string $str2 (optional = '') Имя отправителя. Имеет значение, если $str - это email
     * @return \PlugMail
     */
    public function setFrom($str, $str2 = '') {
        if (strpos($str, '<') !== false) {
            $matches = array();
            preg_match('@(.*)\s?<(.*)>@Ui', $str, $matches);
            if (count($matches)) {
                $this->data['from_name'] = trim($matches[0]);
                $this->data['from_email'] = trim($matches[1]);
            }
        } elseif (strpos($str, '@') !== false) {
            $this->data['from_email'] = trim($str);
            if ($str2) {
                $this->data['from_name'] = trim($str2);
            }
        } else {
            $this->data['from_name'] = trim($str);
        }
        return $this;
    }

    /**
     * Допускается: Иванов Иван или ivanov@mail.ru или Иванов Иван <ivanov@mail.ru>
     * @param string $str
     * @return \PlugMail
     */
    public function setReplyTo($str) {
        if (strpos($str, '<') !== false) {
            $matches = array();
            preg_match('@(.*)\s?<(.*)>@Ui', $str, $matches);
            if (count($matches)) {
                $this->data['reply_name'] = trim($matches[0]);
                $this->data['reply_email'] = trim($matches[1]);
            }
        } elseif (strpos($str, '@') !== false) {
            $this->data['reply_email'] = trim($str);
        } else {
            $this->data['reply_name'] = trim($str);
        }
        return $this;
    }

    /**
     * По умолчанию отправлять как HTML
     */
    public function setAsPlain() {
        $this->data['as_plain'] = true;
        return $this;
    }

    /**
     * По умолчанию уже отправлять как HTML
     */
    public function setAsHTML() {
        $this->data['as_plain'] = false;
        return $this;
    }

    /**
     * 
     * @param string $sender Допускается mail@mail.ru или mail@mail.ru:12345
     * @param string $pass (optional = false) указывается, если в $sender нет пароля
     * @return \PlugMail
     */
    public function setSMTPSender($sender, $pass = false) {
        if (!$pass) {
            $tmp = explode(':', $sender);
            $sender = $tmp[0];
            $pass = $tmp[1];
        }
        $this->data['from_email'] = $sender;
        $this->data['smtp'] = array('login' => $sender, 'pass' => $pass);
        return $this;
    }

    /**
     * Используется чтобы убрать внизу текст "Отвечать на данное письмо ненужно"
     * @param bool $can
     * @return \PlugMail
     */
    public function setCanAnswer($can) {
        $this->data['can_answer'] = $can;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function send() {
        $result = false;

        if (!$this->data['as_plain'] && is_file($_SERVER['DOCUMENT_ROOT'] . '/theme/default/mail.tpl')) {
            ob_start();
            if (!class_exists('ModCity')) {
                @include_once $_SERVER['DOCUMENT_ROOT'] . '/ext/city/modcity.class.php';
            }
            $toEmail = $this->data['to_email'];

            $httpHost = $_SERVER['HTTP_HOST'];
            if (class_exists('ModCity')) {
                $httpHost = ModCity::baseHost();
            }
            $httpHost = "http://$httpHost";

            $mailContent = $this->data['text'];
            $canAnswer = $this->data['can_answer'];
            include $_SERVER['DOCUMENT_ROOT'] . '/theme/default/mail.tpl';
            $this->data['text'] = ob_get_clean();

            $siteParams = SFCore::siteParam();
            foreach ($siteParams as $paramAlias => $paramValue) {
                $this->data['text'] = str_replace('{' . $paramAlias . '}', $paramValue, $this->data['text']);
            }
            $this->data['text'] = str_replace('{year}', date('Y'), $this->data['text']);
            class_exists('ModCity') && $this->data['text'] = ModCity::replaceTemplates($this->data['text']);
            class_exists('ModelCatalog') && $this->data['text'] = ModelCatalog::getInstance()->replaceTemplates($this->data['text']);
        }
        $this->data['text_utf8'] = $this->data['text'];

        if (md5(@iconv('utf-8', 'utf-8', $this->data['subject'])) == md5($this->data['subject'])) {
            $this->data['subject'] = @iconv('utf-8', 'windows-1251', $this->data['subject']);
        }
        if (md5(@iconv('utf-8', 'utf-8', $this->data['text'])) == md5($this->data['text'])) {
            $this->data['text'] = @iconv('utf-8', 'windows-1251', $this->data['text']);
        }

        $toName = $this->data['to_name'];
        if ($toName && md5(@iconv('utf-8', 'utf-8', $toName)) == md5($toName)) {
            $toName = @iconv('utf-8', 'windows-1251', $toName);
        }
        $toEnc = '=?windows-1251?B?' . base64_encode($toName) . '?=';
        $this->data['to_full'] = "$toEnc <{$this->data['to_email']}>";

        $fromName = $this->data['from_name'];
        if ($fromName && md5(@iconv('utf-8', 'utf-8', $fromName)) == md5($fromName)) {
            $fromName = @iconv('utf-8', 'windows-1251', $fromName);
        }
        $fromEnc = '=?windows-1251?B?' . base64_encode($fromName) . '?=';
        $this->data['from_full'] = "$fromEnc <{$this->data['from_email']}>";

        if ($this->data['reply_email']) {
            $replyName = $this->data['reply_name'];
            if ($replyName && md5(@iconv('utf-8', 'utf-8', $replyName)) == md5($replyName)) {
                $replyName = @iconv('utf-8', 'windows-1251', $replyName);
            }
            $replyEnc = '=?windows-1251?B?' . base64_encode($replyName) . '?=';
            $this->data['reply_full'] = "$replyEnc <{$this->data['reply_email']}>";
        }

        $this->data['subject_ready'] = '=?windows-1251?B?' . base64_encode($this->data['subject']) . '?=';

        switch ($this->data['transport']) {
            case self::MAIL_TRANSPORT_DAEMON: $result = $this->sendDaemon();
                break;
            case self::MAIL_TRANSPORT_SMTP: $result = $this->sendSMTP();
                break;
            case self::MAIL_TRANSPORT_FAKE_SUCCESS: $result = true;
                break;
            case self::MAIL_TRANSPORT_FAKE_ERROR: default: $result = false;
        }

        $this->toLog($result);

        return $result;
    }

    private function toLog($result) {
        $q = "DELETE FROM mail_log WHERE ADDDATE(time_create,INTERVAL 1 MONTH) < NOW()";
        SFDB::query($q);
        $set = array("mail_from = '" . SFDB::escape($this->data['from_full']) . "', email = '" . SFDB::escape($this->data['to_full']) . "'");
        $set[] = "text = '" . SFDB::escape($this->data['text_utf8']) . "', subject = '" . SFDB::escape($this->data['subject_ready']) . "'";
        $set[] = "success = " . (int) $result . ", transport = {$this->data['transport']}";
        if (!empty($this->data['reply_full'])) {
            $set[] = "reply_to = '" . SFDB::escape($this->data['reply_full']) . "'";
        }
        $q = "INSERT INTO mail_log SET " . implode(', ', $set);
        $success = SFDB::query($q);
    }

    /**
     * Отправка через стандартную функцию php mail
     * @return type
     */
    private function sendDaemon() {
        $contentType = $this->data['as_plain'] ? 'plain' : 'html';
        $header = "Content-type: text/$contentType; charset=\"windows-1251\"\n";
        $header .= "MIME-Version: 1.0" . "\n";
        $header .= "From: {$this->data['from_full']}\n";
        if ($this->data['reply_email']) {
            $header .= "Reply-To: {$this->data['reply_full']}\n";
        }
        $header .= "Subject: {$this->data['subject_ready']}";
        //$header .= "Content-type: text/$contentType; charset=\"windows-1251\"\n";
        return mail($this->data['to_full'], $this->data['subject_ready'], $this->data['text'], $header);
    }

    /**
     * Отправка через SMTP
     * @return bool 
     */
    private function sendSMTP() {

        $canSend = true;

        if (empty($this->data['smtp']['login']) && !empty(self::$connect['login']) && !empty(self::$connect['pass'])) {
            $this->setSMTPSender(self::$connect['login'], self::$connect['pass']);
        }
        if (empty($this->data['smtp']['login'])) {
            $this->setSMTPSender(SFCore::siteParam('sender'));
        }
        $canSend &=!empty($this->data['smtp']['login']) && !empty($this->data['smtp']['pass']);

        if (!$canSend) {
            return false;
        }

        include_once dirname(__FILE__) . '/smtp_sasl/login_sasl_client.php';
        include_once dirname(__FILE__) . '/smtp_sasl/sasl.php';
        include_once dirname(__FILE__) . '/smtp.class.php';
        $smtp = new smtp();

        $sender_line = __LINE__;
        $recipient_line = __LINE__;

        $smtp->host_name = self::$connect['host'];
        $smtp->host_port = self::$connect['port'];
        $smtp->ssl = 1;
        $smtp->start_tls = 0;
        $smtp->localhost = "localhost";
        $smtp->timeout = 10;
        $smtp->data_timeout = 0;
        $smtp->debug = (int) @$_ENV['debug'];
        $smtp->html_debug = (int) @$_ENV['debug'];
        $smtp->pop3_auth_host = '';
        $smtp->user = $this->data['smtp']['login'];
        $smtp->realm = "";
        $smtp->password = $this->data['smtp']['pass'];
        $smtp->workstation = "";
        $smtp->authentication_mechanism = '';

        setlocale(LC_TIME, null);
        $date = strftime("%a, %d %b %Y %H:%M:%S %z");

        $contentType = $this->data['as_plain'] ? 'plain' : 'html';
        $headers = array(
            "From: {$this->data['from_full']}",
            "To: {$this->data['to_full']}",
            "Subject: {$this->data['subject_ready']}",
            "Date: " . $date,
            "Content-type: text/$contentType; charset=windows-1251"
        );
        if ($this->data['reply_email']) {
            $headers[] = "Reply-To: {$this->data['reply_full']}";
        }

        $res = $smtp->SendMessage($this->data['smtp']['login'], array($this->data['to_email']), $headers, $this->data['text']);
        if (@$_ENV['debug']) {
            echo $smtp->error;
        }
        return $res;
    }

    /**
     * Рекурсивно конвертирует массив или строку. У массива ключи тоже конвертируются
     * @param string|array $mixed
     * @return string|array
     */
    private static function utf2win($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $str) {
                unset($mixed[$index]);
                $mixed[self::utf2win($index)] = self::utf2win($str);
            }
            return $mixed;
        }
        return iconv('utf-8', 'windows-1251', $mixed);
    }

}
