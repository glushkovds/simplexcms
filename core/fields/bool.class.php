<?php

class SFFBool extends SFFInt {

    public $style = 'text-align:center;';
    public $defaultValue = 0;

    public function input($value) {
        return '<span class="sf-bool-span"><input type="checkbox" name="' . $this->inputName() . '" value="1" ' . ($value ? 'checked="checked"' : '') . ($this->readonly ? ' readonly' : '') . ' /></span>';
    }

    public function getPOST($simple = false, $group = null) {
        $postValue = $group === null ? @ $_POST[$this->name] : @ $_POST[$group][$this->name];
        return $this->e2n && empty($postValue) ? 'NULL' : (isset($postValue) ? 1 : 0);
    }

    public function show($row) {
        $value = $row[$this->name];
        echo '<div style="text-align:center"><a class="sff-bool" href="javascript:;">', $value ? 'Да' : 'Нет', '</a></div>';
    }

    public function filter($value) {
        if ($this->filter) {
            echo '
                <div data-toggle="buttons" class="btn-group" style="margin: 0 -4px">
                    <label class="btn btn-default btn-xs' . ($value === '1' ? ' active' : '') . '">
                        <input type="radio" name="filter[' . $this->name . ']" value="1" class="toggle"' . ($value === '1' ? ' checked' : '') . ' onchange="submit()">Да
                    </label>
                    <label class="btn btn-default btn-xs' . ($value === '0' ? ' active' : '') . '">
                        <input type="radio" name="filter[' . $this->name . ']" value="0" class="toggle"' . ($value === '0' ? ' checked' : '') . ' onchange="submit()">Нет
                    </label>
                    <label class="btn btn-default btn-xs' . ($value === '' ? ' active' : '') . '">
                        <input type="radio" name="filter[' . $this->name . ']" value="" class="toggle"' . ($value === '' ? ' checked' : '') . ' onchange="submit()">-
                    </label>
                </div>
            ';
        }
    }

}
