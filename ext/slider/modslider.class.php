<?php

class ModSlider extends SFModBase {
    
    protected function content() {
        $q = "select * from slider where active = 1 order by npp";
        $rows = SFDB::assoc($q);
        PlugJQuery::owlCarousel();
        SFPage::js('/ext/slider/js/slider.js');
        SFPage::css('/ext/slider/css/slider.css');
        include 'slider.tpl';
    }
    
}