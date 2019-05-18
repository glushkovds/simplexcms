<?php

class SFFDateTime extends SFField {

    public function __construct($row) {
        parent::__construct($row);
        $this->defaultValue = !empty($this->params['defaultValue']) ? $this->params['defaultValue'] : date('Y-m-d H:i');
    }

    public function loadUI($onForm = false) {
        if ($onForm) {
            AdminPlugUI::dateTimePicker();
        }
    }

    public function input($value) {
        $value = PlugTime::normal($value, true, false);
        $classes = array("form-control input-medium");
        if (!$this->readonly) {
            $classes[] = "form-datetimepicker";
        }
        return '<input class="' . implode(' ', $classes) . '" type="text" name="' . $this->inputName() . '" value="' . $value . '"' . ($this->readonly ? ' readonly' : '') . ' />';
    }

    public function getPOST($simple = false, $group = null) {
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : '';
        if ($simple) {
            return $value;
        }
        if (preg_match('@^[0-9]{2}.[0-9]{2}.[0-9]{4} [0-9]{2}.[0-9]{2}$@', $value)) {
            $value = substr($value, 6, 4) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2) . ' ' . substr($value, 11, 2) . ':' . substr($value, 14, 2) . ':00';
        }
        return $this->e2n && $value === '' ? 'NULL' : "'" . SFDB::escape($value) . "'";
    }

    public function show($row) {
        $value = $row[$this->name] ? PlugTime::normal($row[$this->name]) : '';
        echo $value;
    }

}
