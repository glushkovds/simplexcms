<?php

class SFFVirtual extends SFField {

    public function __construct($row) {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function show($row) {
        $value = $row[$this->name];
        if ($this->params['text']) {
            $value = $this->params['text'];
            $matches = array();
            preg_match_all("@\{([\w]+)\}@Ui", $value, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    if (isset($row[$match])) {
                        $value = str_replace('{' . $match . '}', $row[$match], $value);
                    }
                }
            }
        }
        if ($this->params['href']) {
            $href = $this->params['href'];
            $matches = array();
            preg_match_all("@\{([\w]+)\}@Ui", $href, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    if (isset($row[$match])) {
                        $href = str_replace('{' . $match . '}', $row[$match], $href);
                    }
                }
            }
            if (@$this->params['in_modal']) {
                $href = "javascript:openModal('$href')";
            }
            $value = '<a href="' . $href . '">' . $value . '</a>';
        }
        echo $value;
    }

    public function getPOST($simple = false, $group = null) {
        return '';
    }

    public function selectQueryField() {
        $subquery = $this->params['subquery'];
        $subquery = $subquery ? "($subquery)" : "''";
        return "$subquery $this->name";
    }

}