<?php

class AdminModBreadCrumbs extends SFModBase {

    protected $name = 'breadcrumbs';

    public function content() {
        $crumbs = SFAdminCore::crumbs();

        if (count($crumbs) > 0) {
            $links = array();
            $cnt = count($crumbs);
            foreach ($crumbs as $i => $row) {
                $links[] = '<a href="' . $row['link'] . '">' . $row['name'] . '</a>';
            }
            include dirname(__FILE__) . '/tpl/crumbs.tpl';
        }
    }

}
