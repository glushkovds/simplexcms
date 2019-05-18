<?php

class ModCallback extends SFModBase {

    protected function content() {

        PlugJQuery::jquery();
        SFPage::js('/ext/callback/js/callback.js');
        SFPage::js('/ext/callback/js/maskedinput.js');
        SFPage::css('/ext/callback/css/callbackform.css');

        if (isset($_POST['phone'])) {
            $phone = isset($_POST['phone']) ? SFDB::escape($_POST['phone']) : '';
            $name = isset($_POST['name']) ? SFDB::escape($_POST['name']) : '';

            $msg = $this->params['message'] ? : "Тел.: {phone}\nФИО: {name}";
            $msg = str_replace("{phone}", $phone, $msg);
            $msg = str_replace("{name}", $name, $msg);

            $to = $this->params['email'] ? : SFCore::siteParam('email');
            $subject = $this->params['subject'] ? : 'Заявка на обратный звонок';
            $success = false;
            $errors = array();
            if (!strlen($name)) {
                $errors[] = "Укажите Ваше имя";
            }
            if (mb_strlen(preg_replace("@[^\d]@", '', $phone)) < 7) {
                $errors[] = "Укажите корректный номер телефона";
            }
            if (!count($errors)) {
                $success = PlugMail::create($to, $subject, $msg)->setAsPlain()->send();
                $sql = "INSERT INTO callback (phone,name) VALUES ('" . $phone . "','" . $name . "')";
                SFDB::query($sql);
            }
            $ret = array('success' => (int) $success, 'error' => implode("<br/>", $errors));
            echo json_encode($ret);
            exit;
        }

        include 'tpl/callbackform.tpl';
    }

}
