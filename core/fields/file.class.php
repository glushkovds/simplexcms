<?php

class SFFFile extends SFField {

    public $path = '';

    public function __construct($row) {
        parent::__construct($row);
        if (!empty($this->params['path'])) {
            $path = $this->params['path'];
            if ($path) {
                $path = substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
                $path = substr($path, -1) == '/' ? $path : $path . '/';
            }
            $this->path = $path;
        }
    }

    public function show($row) {
        if ($row[$this->name]) {
            echo '<a href="/uf/files/' . $this->params['path'] . '/' . $row[$this->name] . '">' . $row[$this->name] . '</a>';
            return;
        }
        parent::show($row);
    }

    public function getValue($value) {
        return $value === '' ? '&nbsp;' : '<a href="/' . $this->path . $value . '" target="_blank">' . $value . '</a>';
    }

    public function loadUI($onForm = false) {
        if ($onForm) {
            AdminPlugUI::fileInput();
        }
    }

    public function input($value) {
        if ($this->readonly) {
            $s = '';
        } else {
            $s = '
                <div class="fileinput fileinput-new" data-provides="fileinput" style="vertical-align: middle">
                    <div class="input-group input-large">
                        <div class="form-control uneditable-input span3" data-trigger="fileinput">
                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                            </span>
                        </div>
                        <span class="input-group-addon btn default btn-file">
                        <span class="fileinput-new"> Выбрать файл </span>
                        <span class="fileinput-exists"> Выбрать файл </span>
                        <input type="file" name="' . $this->name . '">
                        </span>
                        <a href="#" class="input-group-addon btn btn-danger fileinput-exists" data-dismiss="fileinput"> Отмена </a>
                    </div>
                </div>
            ';
            $s .= '<input type="hidden" name="' . $this->name . '_old" value="' . $value . '" />';
        }
        if (is_file('../uf/files/' . $this->path . $value)) {
            $s .= '<p class="form-control-static" style="vertical-align: middle; padding-left: 20px">';
            $s .= '<span class="s11">' . round(filesize('../uf/files/' . $this->path . $value) / 1024) . ' кБ</span>';
            $s .= ' &nbsp; <a href="/uf/files/' . $this->path . $value . '" target="_blank">смотреть</a>';
            if (!$this->required && !$this->readonly) {
                $s .= ' &nbsp; <a href="javascript:;" onclick="deleteField(this)">удалить</a>';
            }
            $s .= '</p>';
        }
        return $s;
    }

    public function getPOST($simple = false, $group = null) {
        $name = isset($_REQUEST[$this->name . '_old']) ? $_REQUEST[$this->name . '_old'] : '';
        $file = new SFFile($this->path);
        if ($file->loadPost($this->name) && $name) {
            $file->delete($name);
        }
        return "'" . $file->getName() . "'";
    }

    public function delete($name) {
        if ($this->required || $this->readonly) {
            return false;
        }
        $file = new SFFile($this->path);
        return $file->delete($name);
    }

    public function check() {
        $errors = array();
        if (SFAdminCore::ajax()) {
            if ($this->required && empty($_REQUEST[$this->name]) && empty($_REQUEST[$this->name . '_old'])) {
                $errors[] = 'Обязательно для заполнения';
                ;
            }
        } else {
            if ($this->required && (empty($_REQUEST[$this->name . '_old']) && !is_uploaded_file($_FILES[$this->name]['tmp_name']))) {
                $errors[] = 'Обязательно для заполнения';
            }
        }
        return $errors;
    }

}
