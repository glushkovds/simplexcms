<?php

class SFAdminNotifyItem {

    private $text = '';
    private $icon = 'bolt';
    private $href = 'javascript:;';
    private $data = array();

    public function __construct($text, $href = '', $icon = '') {
        $this->text = $text;
        $icon ? $this->icon = $icon : null;
        $href ? $this->href = $href : null;
    }

    public function html() {
        include 'tpl/item.tpl';
    }

}
