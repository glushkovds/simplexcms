<?php

class SFFDouble extends SFField {

    private function doubleFormat($value, $showMode = false) {
        if ($showMode && !$value || $value === null) {
            return '';
        }
        $decimals = $this->params['decimals'];
        if (!$decimals) {
            $tmp = explode('.', (double) $value);
            $decimals = isset($tmp[1]) ? strlen($tmp[1]) : 0;
        }
        return number_format((double) $value, $decimals, $this->params['dec_point'], $showMode ? $this->params['thousands_sep'] : '');
    }

    public function input($value) {
        $ret = '<input class="form-control" type="text" name="' . $this->inputName() . '" value="' . $this->doubleFormat($value) . '"' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"') . ($this->readonly ? ' readonly' : '') . ' />' . "\n";
//        help выводится в tpl
        return $ret;
    }

    public function show($row) {
        $value = $this->doubleFormat($row[$this->name], true);
        echo '<span style="white-space: nowrap">' . $value . '</span>';
    }

    public function getPOST($simple = false, $group = null) {
        $ret = $_POST[$this->name];
        if ($simple) {
            $ret = $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
        }
        $ret = str_replace(',', '.', $ret);
        $ret = preg_replace("@[^\d+\.]@", '', $ret);
        return $this->e2n && $ret === '' ? 'NULL' : $ret;
    }

}