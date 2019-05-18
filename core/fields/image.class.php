<?php

class SFFImage extends SFFFile {

    public $sizes = array();

    public function __construct($row) {
        parent::__construct($row);
        if (!empty($this->params['small'])) {
            $this->sizes['small'] = $this->params['small'];
        }
        if (!empty($this->params['medium'])) {
            $this->sizes['medium'] = $this->params['medium'];
        }
        if (!empty($this->params['large'])) {
            $this->sizes['large'] = $this->params['large'];
        }
    }

    public function loadUI($onForm = false) {
        parent::loadUI($onForm);
        if (!$onForm) {
            PlugJQuery::plugFancybox();
        }
    }

    public function input($value) {

        // http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image
        // /admin/theme/img/noimage.200x150.gif

        $thumb = $thumbNoImage = "/admin/theme/img/noimage.90x90.gif";
        $source = '';
        $imgPath = 'uf/images/' . $this->path . 'preview/' . $value;
        if (is_file("../$imgPath")) {
            $thumb = "/$imgPath";
            $source = '/uf/images/' . $this->path . 'source/' . $value;
        }

        $s = '<div class="fileinput fileinput-new" data-provides="fileinput" style="float: left">';
        $s.='
            <div class="fileinput-new thumbnail" style="max-width: 90px; max-height: 90px; float: left">
                ' . ($source ? '<a href="' . $source . '" class="fancybox"><img src="' . $thumb . '" alt="" /></a>' : '<img src="' . $thumb . '" alt="" />') . '
                <input type="hidden" class="thumb-noimage" value="' . $thumbNoImage . '" />
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 90px; max-height: 90px; float: left"></div>
        ';
        if (!$this->readonly) {
            $s.='
                <div style="padding-left: 15px; float: left">
                    <span class="btn btn-default btn-file">
                        <span class="fileinput-new"> Выбрать изображение </span>
                        <span class="fileinput-exists"> Выбрать изображение </span>
                        <input type="file" name="' . $this->name . '">
                    </span>
                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"> Отмена </a>
                </div>
            ';
            $s.= '<input type="hidden" name="' . $this->name . '_old" value="' . $value . '" />';
        }
        $s.= '</div>';
        if (is_file("../uf/images/{$this->path}source/$value")) {
            $s .= '<p class="form-control-static" style="display: block; float: left; padding-left: 20px">';
            $s .= '<span class="s11">' . round(filesize("../uf/images/{$this->path}source/$value") / 1024) . ' кБ</span>';
            if (!$this->required && !$this->readonly) {
                $s .= ' &nbsp; <a href="javascript:;" onclick="deleteField(this)">удалить</a>';
            }
            $s .= '</p>';
        }
        $s .= '<div class="clearfix"></div>';
        return $s;
    }

    public function getPOST($simple = false, $group = null) {
        $name = isset($_REQUEST[$this->name . '_old']) ? $_REQUEST[$this->name . '_old'] : '';
        $img = new SFImage($this->path, $this->sizes);
        $img->loadPost($this->name);
        $name_new = $img->getName();
        if ($name_new) {
            if ($name) {
                $img->delete($name);
            }
            $img->save();
            $name = $name_new;
        }
        return "'$name'";
    }

    public function show($row) {
        $value = $row[$this->name];
        if (is_file('../uf/images/' . $this->path . 'source/' . $value)) {
            echo '<a class="sff-image" href="/uf/images/' . $this->path . 'source/' . $value . '" target="_blank"><img src="/uf/images/' . $this->path . 'preview/' . $value . '" alt="' . $value . '" /></a>';
        }
    }

    public function delete($value) {
        if ($this->required || $this->readonly) {
            return false;
        }
        $img = new SFImage($this->path);
        return $img->delete($value);
    }

}