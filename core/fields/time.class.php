<?php

class SFFTime extends SFField {

    private $pattern;

    public function __construct($row) {
        parent::__construct($row);
        $this->pattern = @$this->params['use_seconds'] ? 'H:i:s' : 'H:i';
        $this->defaultValue = $this->e2n ? '' : date($this->pattern);
        if (!empty($this->params['defaultValue'])) {
            $this->defaultValue = $this->params['defaultValue'];
        }
    }

    public function loadUI($onForm = false) {
        if ($onForm) {
            AdminPlugUI::timePicker();
        }
    }

    public function input($value) {
        $value = $value ? PlugTime::convert($value, $this->pattern, false) : '';
        $classes = array("form-control");
        if (!$this->readonly) {
            $classes[] = "form-timepicker" . (@$this->params['use_seconds'] ? '-seconds' : '');
        }
        return '<input class="' . implode(' ', $classes) . '" type="text" name="' . $this->inputName() . '" value="' . $value . '"' . ($this->readonly ? ' readonly' : '') . ' />';
    }

    public function getPOST($simple = false, $group = null) {
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : '';
        if ($simple) {
            return $value;
        }
        $ret = $this->e2n && $value === '' ? 'NULL' : "'" . PlugTime::convert($value, $this->pattern, false) . "'";
        return $ret;
    }

    public function show($row) {
        $value = $row[$this->name] ? PlugTime::convert($row[$this->name], $this->pattern, false) : '';
        echo $value;
    }

}

