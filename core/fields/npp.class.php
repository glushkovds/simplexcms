<?php

class SFFNPP extends SFField {

    public function show($row) {
        $value = $row[$this->name];
        $pkValue = $row[$this->tablePk];
        echo '<div class="npp-show-field">';
        echo '<a href="?action=change_npp&field=' . $this->name . '&' . $this->tablePk . '=' . $pkValue . '&down"><i class="fa  fa-caret-down"></i></a>&nbsp; ';
        echo $value;
        echo ' &nbsp;<a href="?action=change_npp&field=' . $this->name . '&' . $this->tablePk . '=' . $pkValue . '&up"><i class="fa fa-caret-up"></i></a>';
        echo '</div>';
    }

    public function input($value) {
        if (AdminBase::$isAdd) {
            $where = array();
            if ($filter = @$_SESSION[$this->table]['filter']) {
                foreach ($filter as $fname => $fval) {
                    if ($fval) {
                        $where[] = "$fname = '$fval'";
                    }
                }
            }
            $q = "SELECT MAX($this->name) FROM $this->table" . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
            $max = SFDB::result($q, 0);
            $value = $max + 1;
        }
        return parent::input($value);
    }

}