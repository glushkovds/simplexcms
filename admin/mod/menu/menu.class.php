<?php

class AdminModMenu extends SFModBase {

    protected $name = 'menu';

    public function content() {
        $menu = SFAdminCore::menu();
        include dirname(__FILE__) . '/tpl/menu.tpl';
    }

}
