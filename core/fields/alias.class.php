<?php

class SFFAlias extends SFField {

    public function getPOST($simple = false, $group = null) {
        $val = $_POST[$this->name];
        if (empty($val) && !empty($this->params['source'])) {
            $val = isset($_POST[$this->params['source']]) ? PlugFunc::translite($_POST[$this->params['source']]) : '';
        }
        $ret = $this->e2n && $val === '' ? 'NULL' : "'" . SFDB::escape($val) . "'";
        return $ret;
    }

    public function defval() {
        return '';
    }

}