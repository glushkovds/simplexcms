<?php

class SFForeignKey {

    public $table = '';
    public $key = '';
    public $label = '';
    public $is_pid = false;

    public function __construct($params) {
        $this->table = $params['fk_table'];
        $this->key = $params['fk_key'];
        $this->label = $params['fk_label'];
        $this->is_pid = !empty($params['fk_is_pid']);
    }

}
