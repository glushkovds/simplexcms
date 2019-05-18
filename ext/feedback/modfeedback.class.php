<?php

/**
 * ModContent class
 *
 * Output last contents
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class ModFeedback extends SFModBase {

    protected function content() {
        $form = new PlugForm('feedback');
        $form->btnSubmit('Отправить');

        $field = PlugForm::newField('name', 'Представьтесь', 'Как к Вам обращаться?');
        $field->v_requied['is'] = 1;
        $form->addField($field);

        $field = PlugForm::newFieldEmail('email', 'Ваш e-mail', 'Адрес, на который будет отправлен ответ');
        $field->v_requied['is'] = 1;
        $form->addField($field);

        $field = PlugForm::newFieldText('message', 'Сообщение', 'Ваше сообщение, вопрос или пожелание');
        $field->v_requied['is'] = 1;
        $form->addField($field);

        $form->addSpamcode();

        $sended = false;

        PlugAlert::bufferOn();

        if ($form->submitted()) {
            if ($form->validate()) {
                $to = $this->params['email'];
                $subject = $this->params['subject'];
                $name = $form->getPOST('name');
                $email = $form->getPOST('email');
                $message = $form->getPOST('message');

                $rows = array();
                $rows[] = $message;
                $rows[] = "";
                $rows[] = "---";
                $rows[] = "С уважением, " . $name;
                if (PlugMail::create($to, $subject, join("\r\n", $rows))->setAsPlain()->setFrom($email, $name)->send()) {
                    $sended = true;
                    PlugAlert::ok('Ваше сообщение отправлено, Мы ответим Вам в ближайшее время');
                } else {
                    PlugAlert::warning('Сообщение не может быть отправлено по техническим причинам, попробуйте позднее');
                }
            } else {
                PlugAlert::warning('При заполнении формы возникли ошибки');
            }
        }

        include dirname(__FILE__) . '/tpl/form.tpl';
    }

}
