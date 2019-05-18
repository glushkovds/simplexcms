<?php

class SFFDate extends SFField {

    public function __construct($row) {
        parent::__construct($row);
        $this->defaultValue = $this->e2n ? '' : date('Y-m-d');
        if (!empty($this->params['defaultValue'])) {
            $this->defaultValue = $this->params['defaultValue'];
        }
    }

    public function loadUI($onForm = false) {
        if ($onForm) {
            AdminPlugUI::datePicker();
        }
    }

    public function input($value) {
        $value = PlugTime::normal($value); //substr($value, 8, 2) . '.' . substr($value, 5, 2) . '.' . substr($value, 0, 4);
        $classes = array("form-control");
        if (!$this->readonly) {
            $classes[] = "form-datepicker";
        }
        return '<input class="' . implode(' ', $classes) . '" type="text" name="' . $this->inputName() . '" value="' . $value . '"' . ($this->readonly ? ' readonly' : '') . ' />';
    }

    public function getPOST($simple = false, $group = null) {
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : '';
        if ($simple) {
            return $value;
        }
        if (preg_match('@^[0-9]{2}.[0-9]{2}.[0-9]{4}$@', $value)) {
            $value = substr($value, -4) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2);
        }
        return $this->e2n && $value === '' ? 'NULL' : "'" . SFDB::escape($value) . "'";
    }

    public function show($row) {
        $value = $row[$this->name] ? PlugTime::normal($row[$this->name]) : '';
        echo $value;
    }

}