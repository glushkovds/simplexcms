<?php

class SFField
{

    public $form = '';
    public $name = '';
    public $label = '';
    public $pk = false;
    public $table = '';
    public $tablePk = '';
    public $help = '';
    public $placeholder = '';
    public $params = array();

    /**
     *
     * @var SFForeignKey
     */
    public $fk = false;
    public $width = 0;
    public $isVisible = true;
    public $link = '';
    public $isnull = false;
    public $e2n = false;
    public $defaultValue = '';
    public $autoIncrement = false;
    public $required = 0;
    public $editor = '';
    public $filter = 0;

    /* INPUT */
    public $styleCol = [];
    public $styleCell = '';
    public $hidden = false;
    public $readonly = false;
    public $onchange = '';
    protected $tree = array();
    public $value = '';
    protected $pid;

    /**
     *
     * @var bool
     */
    public $isVirtual = false; // SFFVirtual, SFFMultiKey

    public function __construct($row)
    {

        $this->name = $row['name'];
        $this->label = $row['label'];
        $this->table = $row['table'];
        $this->help = (string)@$row['help'];
        $this->placeholder = (string)@$row['placeholder'];
        if (isset($row['params']) && is_string($row['params'])) {
            $row['params'] = unserialize($row['params']);
        }
        $params = isset($row['params']['main']) ? $row['params']['main'] : array();
        $this->params = $params;

        $this->setWidth(@$params['width']);

        if (!empty($params['pk'])) {
            $this->pk = (bool)$params['pk'];
        }
        if (!empty($params['is_fk'])) {
            $this->fk = new SFForeignKey($params);
        }
        if (!empty($params['style_col'])) {
            $this->styleCol['from_param'] = $params['style_col'];
        }
        if (!empty($params['style_cell'])) {
            $this->styleCell .= $params['style_cell'];
        }
        if (!empty($params['link'])) {
            $this->link = $params['link'];
        }
        if (!empty($params['hidden'])) {
            $this->hidden = (bool)$params['hidden'];
        }
        if (!empty($params['e2n'])) {
            $this->e2n = (bool)$params['e2n'];
            $this->isnull = (bool)$params['e2n'];
        }
        if (!empty($params['isnull'])) {
            $this->isnull = (bool)$params['isnull'];
        }
        if (!empty($params['defaultValue'])) {
            $this->defaultValue = $params['defaultValue'];
        }
        if (!empty($params['autoIncrement'])) {
            $this->autoIncrement = $params['autoIncrement'];
        }
        if (!empty($params['required'])) {
            $this->required = (bool)(int)$params['required'];
        }
        if (!empty($params['readonly'])) {
            $this->readonly = (bool)(int)$params['readonly'];
        }
        if (!empty($params['editor'])) {
            $this->editor = $params['editor'];
        }

        if (!empty($params['filter'])) {
            $this->filter = $params['filter'];
        }

        if (!empty($params['onchange'])) {
            $this->onchange = $params['onchange'];
        }
    }

    /**
     *
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = (empty($width) || (int)$width == 0) ? 0 : (double)$width;
        $this->isVisible = $this->width > 0;
        $this->styleCol['width'] = $this->width > 1 ? 'width: ' . $this->width . 'px' : (($this->width < 1 && $this->width > 0) ? 'width: ' . round($this->width * 100) . '%' : '');
    }

    public static function setFieldValue(&$field, $group, $params, $row)
    {
        $p = false;
//        if (is_string($field)) {
//            $field = $this->fields[$field];
//            $p = true;
//        }
        $value = '';
        if ($group['name']) {
            $value = isset($params[$group['name']][$field->name]) ? $params[$group['name']][$field->name] : $field->defaultValue;
        } else {
            $value = isset($params[$field->name]) ? $params[$field->name] : $field->defaultValue;
        }
        if ($p) {
            $value = $row[$field->name];
        }
        $field->value = $value;
    }

    public function input($value)
    {
        $ret = '<input class="form-control" type="text" name="' . $this->inputName() . '" value="' . htmlspecialchars($value) . '"' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"') . ($this->readonly ? ' readonly' : '') . ' />' . "\n";
//        help выводится в tpl
        return $ret;
    }

    public function inputName()
    {
        return $this->form ? $this->form . '[' . $this->name . ']' : $this->name;
    }

    public function inputHidden($value)
    {
        return '<input type="hidden" name="' . $this->inputName() . '" value="' . htmlspecialchars($value) . '" />';
    }

    public function getPOST($simple = false, $group = null)
    {
        if ($simple) {
            return $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
        }
        return $this->e2n && $_POST[$this->name] === '' ? 'NULL' : "'" . SFDB::escape($_POST[$this->name]) . "'";
    }

    public function check()
    {
        $errors = array();
        if ($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] === '')) {
            $errors[] = 'Обязательно для заполнения';
        }
        return $errors;
    }

    public function show($row)
    {
        $value = $row[$this->name . ($this->fk ? '_label' : '')];
        if ($this->pk) {
            echo '<a href="javascript:openModal(\'?action=showDetail&id=' . htmlspecialchars($value) . '\',function(){$(\'.modal-dialog\').addClass(\'modal-wide\');})">' . $value . '</a>';
        } else {
            echo $value;
        }
    }

    /**
     * Show in detail card in show mode
     * @param $row
     * @return mixed
     */
    public function showDetail($row)
    {
        $value = $row[$this->name . ($this->fk ? '_label' : '')];
        return $value;
    }

    public function delete($value)
    {
        return true;
    }

    public function defval()
    {
        return $this->defaultValue;
    }

    public function filter($value)
    {
        if ($this->filter) {
            $inExtra = $this->width == 0;
            echo '<input type="text" class="form-control" name="filter[' . $this->name . ']" placeholder="' . ($inExtra ? htmlspecialchars($this->label) : '') . '" value="' . htmlspecialchars($value) . '" />';
        }
    }

    public function loadUI($onForm = false)
    {

    }

    public function selectQueryField()
    {
        return '' . $this->table . '.' . $this->name;
    }

}