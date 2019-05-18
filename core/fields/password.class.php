<?php

class SFFPassword extends SFField {

    public function __construct($row) {
        parent::__construct($row);
        if (empty($this->help)) {
            $this->help = 'Оставьте пустым, если не требуется изменить';
        }
    }

    public function input($value) {
        return '<input class="form-control" type="text" name="' . $this->inputName() . '" value=""' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"') . ($this->readonly ? ' readonly' : '') . ' />';
    }

    public function getPOST($simple = false, $group = null) {
        return empty($_POST[$this->name]) ? '' : "'" . md5($_POST[$this->name]) . "'";
    }

}
