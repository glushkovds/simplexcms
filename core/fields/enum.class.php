<?php

class SFFEnum extends SFFString
{

    protected $changeURL;

    public function input($value)
    {

        $this->value = $value;
        $disabled = 1 ? "" : " disabled";
        $this->select = '<select' . $disabled . ' class=" form-control"' . ($this->onchange ? ' onchange="' . $this->onchange . '"' : '') . ' name="' . $this->name . '"' . ($this->readonly ? ' readonly' : '') . '>';
        $values = $this->fetchValues($this->table, $this->name);
        if ($this->e2n) {
            $this->select .= '<option value=""></option>';
        }
        foreach ($values as $valAlias => $val) {
            $selected = $value == $valAlias ? ' selected' : '';
            $this->select .= '<option value="' . $valAlias . '"' . $selected . '>' . $val . '</option>';
        }
        $this->select .= '</select>';
        return $this->select;
    }

    protected function fetchValues()
    {
        $buffer = &$_ENV['enum_values'][$this->table][$this->name];

        if (!isset($buffer)) {
            $q = "SHOW FULL COLUMNS FROM `$this->table` LIKE '$this->name'";
            $row = SFDB::result($q);
            $enumArray = array();
            preg_match_all("/'(.*?)'/", $row['Type'], $enumArray);
            $enumFields = $enumArray[1];
            $names = explode(';;', $row['Comment']);
            if (count($names) == count($enumFields)) {
                $ret = array();
                foreach ($names as $index => $name) {
                    $ret[$enumFields[$index]] = trim($name);
                }
                $buffer = $ret;
            } else {
                $buffer = array();
                foreach ($enumFields as $name) {
                    $buffer[$name] = $name;
                }
            }
        }

        return $buffer;
    }

    public function filter($value)
    {
        if ($this->filter) {
            $disabled = 1 ? "" : " disabled";
            $select = '<select' . $disabled . ' class="form-control" name="filter[' . $this->name . ']" onchange="submit()">';
            $select .= '<option value="">---' . $this->label . '---</option>';
            $values = $this->fetchValues();
            foreach ($values as $key => $val) {
                $selected = $value == $key ? ' selected' : '';
                $select .= '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
            }
            $select .= '</select>';
            echo $select;
        }
    }

    public function showDetail($row)
    {
        $value = $row[$this->name];
        $values = $this->fetchValues();
        return @$values[$value];
    }

    public function show($row)
    {
        $value0 = $row[$this->name];
        $pkValue = $row[$this->tablePk];
        $values = $this->fetchValues();

        $showValue = @$values[$value0];
        if ($showValue === null) {
            $showValue = '<i>(null)</i>';
        }

        $minWidth = max(80, $this->width);

        $value = '<div class="enum-show-field btn-group">';
        $value .= '<button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"' . ($this->readonly ? ' title="Только для чтения"' : '') . '>' . $showValue . '</button>';
        if (!$this->readonly) {
            $value .= '<ul role="menu" class="dropdown-menu" style="min-width: ' . $minWidth . 'px; left: -12px">';
            if ($this->e2n) {
                $value .= '<li><a class="a-status-change" href="?action=change_enum&field=' . $this->name . '&' . $this->tablePk . '=' . $pkValue . '&newstatus="><i>(null)</i></a></li>';
            }
            foreach ($values as $key => $val) {
                $value .= '<li><a class="a-status-change" href="?action=change_enum&field=' . $this->name . '&' . $this->tablePk . '=' . $pkValue . '&newstatus=' . $key . '">' . $val . '</a></li>';
            }
            $value .= '</ul>';
        }
        $value .= '</div>';
        echo $value;
    }

}
