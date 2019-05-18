<?php

/**
 * PlugForm class
 *
 * Allow to create html forms and validate them
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class PlugForm {

    private $name;
    private $action = '';
    private $method = 'post';
    private $fields = array();
    private $errors = array();
    private $display = true;
    private $btn_submit = 'Submit';

    public function __construct($name, $action = '', $method = 'post') {
        $this->name = $name;
        $this->action = $action;
        $this->method = $method;
    }

    public static function init() {
//        SFPage::css('/plug/form/form.css');
    }

    public function btnSubmit($value) {
        $this->btn_submit = $value;
    }

    public function addField($field) {
        if ($field instanceof PlugFormField) {
            $field->form = $this->name;
            $this->fields[$field->name] = $field;
        }
    }

    public function addSpamcode() {
        $field = new PlugFormFieldSpamcode('spamcode', 'Спам-код', '');
        $this->addField($field);
    }

    public function html() {
        if ($this->display) {
//            SFPage::css('/plug/form/form.css');
            include dirname(__FILE__) . '/form.tpl';
        }
    }

    public function submitted() {
        return isset($_POST[$this->name]['submit']);
    }

    public function validate() {
        $errors = array();
        foreach ($this->fields as $field) {
            $errors = array_merge($errors, $field->check());
        }
        $this->errors = $errors;

        return count($this->errors) ? false : true;
    }

    public function errors($asString = false) {
        if ($asString) {
            $plain = [];
            foreach ($this->errors as $fname => $errors) {
                $field = $this->fields[$fname];
                $plain[] = trim(str_replace('*', '', ($field->label ? : $field->placeholder))) . ': ' . implode(', ', $errors);
            }
            return implode(', ', $plain);
        }
        return $this->errors;
    }

    public function hide() {
        $this->display = false;
    }

    public function show() {
        $this->display = true;
    }

    public function display() {
        return $this->display;
    }

    public function getPOST($fieldname) {
        return isset($this->fields[$fieldname]) ? $this->fields[$fieldname]->getPost() : '';
    }

    /**
     * @return PlugFormField
     */
    public static function newField($name, $label, $comment = '') {
        return new PlugFormField($name, $label, $comment);
    }

    public static function newFieldText($name, $label, $comment = '') {
        return new PlugFormField($name, $label, $comment, 1);
    }

    /**
     * @return PlugFormField
     */
    public static function newFieldCheckbox($name, $label, $comment = '') {
        return new PlugFormFieldCheckbox($name, $label, $comment);
    }

    /**
     * @return PlugFormFieldPass
     */
    public static function newFieldPass($name, $label, $comment = '') {
        return new PlugFormFieldPass($name, $label, $comment);
    }

    /**
     * @return PlugFormFieldEmail
     */
    public static function newFieldEmail($name, $label, $comment = '') {
        return new PlugFormFieldEmail($name, $label, $comment);
    }

    public function getFields() {
        return $this->fields;
    }

}

class PlugFormField {

    public $form;
    public $name;
    public $label;
    public $placeholder;
    public $comment;
    public $is_text = 0;
    public $required = false;
    public $v_requied = array();
    public $v_length = array();
    public $v_equal = array();
    public $v_mask = array();
    public $v_unique = array();
    protected $defval = '';

    public function __construct($name, $label, $comment, $is_text = 0, $placeholder = '', $required = false) {
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->comment = $comment;
        $this->is_text = $is_text;
        $this->required = $required;
    }

    public function html() {
        $val = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : $this->defval;
        $inputParams = array('class' => 'edit', 'type' => 'text', 'name' => "$this->form[$this->name]");
        $inputParams['value'] = htmlspecialchars($val);
        $this->placeholder && $inputParams['placeholder'] = $this->placeholder;
        $this->required && $inputParams['required'] = 'required';
        $tmp = array();
        foreach ($inputParams as $key => $value) {
            $tmp[] = $key . '="' . htmlspecialchars($value) . '"';
        }
        if ($this->is_text) {
            echo '<span class="plug-form-field"><textarea ' . implode(' ', $tmp) . ' cols="20" rows="4">', htmlspecialchars($val), '</textarea></span>';
        } else {
            echo '<span class="plug-form-field"><input ' . implode(' ', $tmp) . ' /></span>';
        }
    }

    public function &check() {
        $err = array();
        $_POST[$this->form][$this->name] = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : '';

        /* REQUIED */
        $this->v_requied = is_array($this->v_requied) ? $this->v_requied : array();
        if (!empty($this->v_requied['is']) && $_POST[$this->form][$this->name] === '') {
            $err[$this->name][] = empty($this->v_requied['comment']) ? "обязательно для заполнения" : $this->v_requied['comment'];
        }

        if (empty($err[$this->name])) {
            /* LENGTH */
            $len = mb_strlen($_POST[$this->form][$this->name]);
            $this->v_length = is_array($this->v_length) ? $this->v_length : array();
            $this->v_length['min'] = isset($this->v_length['min']) ? (int) $this->v_length['min'] : 0;
            $this->v_length['max'] = isset($this->v_length['max']) ? (int) $this->v_length['max'] : 0;
            if (($this->v_length['min'] > 0 && $this->v_length['min'] > $len) || ($this->v_length['max'] > 0 && $this->v_length['max'] < $len)) {
                $err[$this->name][] = empty($this->v_length['comment']) ? "недопустимая длина" : $this->v_length['comment'];
            }

            /* EQUAL */
            $this->v_equal = is_array($this->v_equal) ? $this->v_equal : array();
            $this->v_equal['field'] = isset($this->v_equal['field']) ? (string) $this->v_equal['field'] : '';
            if ($this->v_equal['field']) {
                $_POST[$this->form][$this->v_equal['field']] = isset($_POST[$this->form][$this->v_equal['field']]) ? $_POST[$this->form][$this->v_equal['field']] : '';
                if ($_POST[$this->form][$this->name] !== $_POST[$this->form][$this->v_equal['field']]) {
                    $err[$this->name][] = empty($this->v_equal['comment']) ? "неверное значение" : $this->v_equal['comment'];
                }
            }

            /* MASK */
            if ($_POST[$this->form][$this->name] !== '') {
                $this->v_mask = is_array($this->v_mask) ? $this->v_mask : array();
                $this->v_mask['pattern'] = isset($this->v_mask['pattern']) ? (string) $this->v_mask['pattern'] : '';
                if ($this->v_mask['pattern'] && !preg_match($this->v_mask['pattern'], $_POST[$this->form][$this->name])) {
                    $err[$this->name][] = empty($this->v_mask['comment']) ? "некорректное значение" : $this->v_mask['comment'];
                }
            }

            /* UNIQUE */
            $this->v_unique = is_array($this->v_unique) ? $this->v_unique : array();
            $this->v_unique['key'] = isset($this->v_unique['key']) ? (string) $this->v_unique['key'] : '';
            if ($this->v_unique['key'] && preg_match('@([a-z0-9\_]+)\.([a-z0-9\_]+)@', $this->v_unique['key'], $key)) {
                $q = "SELECT COUNT(*) cnt FROM #__" . $key[1] . " WHERE " . $key[2] . "='" . SFDB::escape($_POST[$this->form][$this->name]) . "'";
                if ((int) SFDB::result($q, 'cnt') > 0) {
                    $err[$this->name][] = empty($this->v_unique['comment']) ? "занято" : $this->v_unique['comment'];
                }
            }
        }

        return $err;
    }

    public function getPOST() {
        return isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : '';
    }

    public function defval($value) {
        $this->defval = $value;
    }

}

class PlugFormFieldCheckbox extends PlugFormField {

    public function html() {
        $val = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : $this->defval;
        echo '<span class="plug-form-field"><input type="checkbox" name="', $this->form, '[', $this->name, ']', '" ', empty($val) ? '' : 'checked="checked"', ' /></span>';
    }

}

class PlugFormFieldPass extends PlugFormField {

    public function html() {
        $val = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : '';
        echo '<span class="plug-form-field"><input type="password" name="', $this->form, '[', $this->name, ']', '" value="', htmlspecialchars($val), '" /></span>';
    }

}

class PlugFormFieldEmail extends PlugFormField {

    public $v_mask = array(
        'pattern' => '/^[a-z0-9_\-\.]{1,20}@[a-z0-9\-\.]{1,20}\.[a-z]{2,8}$/i'
    );

}

class PlugFormFieldText extends PlugFormField {
    
}

class PlugFormFieldSpamcode extends PlugFormField {

    public $v_requied = 1;

    public function html() {
        $val = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : '';
        echo '<span class="plug-form-spamcode">';
        PlugSpamcode::html($this->form);
        echo '</span>';
    }

    public function &check() {
        $err = array();
        $_POST[$this->form][$this->name] = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : '';
        if ($_POST[$this->form][$this->name] === '' || $_POST[$this->form][$this->name] !== $_SESSION['spamcode']) {
            $err[$this->name][] = "спам-код введен неверно";
        }
        return $err;
    }

}
