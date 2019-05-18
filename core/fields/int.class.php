<?php

class SFFInt extends SFField {

    public $defaultValue = 0;

    public function &tree() {
        $tree = array();
        $q = "
            SELECT *,
                (SELECT class FROM struct_field WHERE field_id = s.field_id) class
            FROM struct_data s
            WHERE table_id = (SELECT table_id FROM struct_table WHERE name = '{$this->fk->table}')
        ";
        $fkTableFields = SFDB::assoc($q);
        $fkPID = 0;
        foreach ($fkTableFields as $fkTableField) {
            if ($fkTableField['class'] == 'SFFInt') {
                $fkParams = unserialize($fkTableField['params']);
                if (!empty($fkParams['main']['fk_is_pid'])) {
                    $fkPID = $fkTableField['name'];
                    break;
                }
            }
        }
        $q = "
            SELECT {$this->fk->key} id, $fkPID pid, {$this->fk->label} label
            FROM {$this->fk->table}
            " . (isset($_REQUEST[$this->fk->key]) ? "WHERE " . $this->fk->key . "<>" . (int) $_REQUEST[$this->fk->key] : "") . "
            ORDER BY " . $this->fk->key . "
        ";
        $r = SFDB::query($q);
        while ($row = SFDB::fetch($r)) {
            $tree[(int) $row['pid']][(int) $row['id']] = $row;
        }
        return $tree;
    }

    public function input($value) {
        if ($this->fk) {
            $tree = $this->tree();
            $list = SFService::tree2list($tree);
            $select = '<select class="form-control" name="' . $this->inputName() . '"' . ($this->onchange ? ' onchange="' . $this->onchange . '"' : '') . ($this->readonly ? ' readonly' : '') . '>';
            $select.= '<option value="">&nbsp;</option>';
            foreach ($list as $id => $row) {
                $select.= '<option value="' . $id . '"' . ($id == $value ? ' selected="selected"' : '') . '>' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $row['tree_level']) . $row['label'] . '</option>';
            }
            $select.= '</select>';
            return $select;
        }
        return '<input class="form-control" type="text" name="' . $this->inputName() . '" value="' . htmlspecialchars($value) . '"' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"') . ($this->readonly ? ' readonly' : '') . ' />';
    }

    public function show($row) {
        $value = $row[$this->name . ($this->fk ? '_label' : '')];
        echo $value;
    }

    public function getPOST($simple = false, $group = null) {
        return $this->e2n && !$_POST[$this->name] ? 'NULL' : (int) $_POST[$this->name];
    }

    public function filter($value) {
        if ($this->filter) {
            if ($this->fk) {
                $tree = $this->tree();
                $list = SFService::tree2list($tree);

                $select = '<select class="form-control" name="filter[' . $this->name . ']" onchange="submit()">';
                $select.= '<option value="">---' . $this->label . '---</option>';
                if ($this->isnull) {
                    $select.= '<option value="null" ' . ($value === 'null' ? 'selected="selected"' : '') . '>NULL</option>';
                }
                foreach ($list as $id => $row) {
                    $select.= '<option value="' . $id . '"' . ($id == $value ? ' selected="selected"' : '') . '>' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $row['tree_level']) . $row['label'] . '</option>';
                }
                $select.= '</select>';
                echo $select;
            } else {
                return parent::filter($value);
            }
        }
    }

}
