<?php

class PlugJQuery {

    public static function jquery() {
        SFPage::js('//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', 0);
//        SFPage::js('http://code.jquery.com/jquery-migrate-1.4.0.js', 0);
    }

    public static function fancybox() {
        SFPage::js('/plug/jquery/plugins/fancybox/jquery.fancybox.pack.js', 10);
        SFPage::css('/plug/jquery/plugins/fancybox/jquery.fancybox.css', 10);
        SFPage::js('/plug/jquery/plugins/fancybox/jquery.fancybox.simplex.js', 10);
//        PlugFrontEnd::js("$(function(){\$('.fancybox, .lightview').fancybox()})");
//        PlugFrontEnd::js("$(function(){\$('.fancybox, .lightview').fancybox({helpers: {overlay: {locked: false}}})})");
    }

    public static function owlCarousel() {
        SFPage::css('/plug/jquery/plugins/owl-carousel/owl.carousel.css');
        SFPage::js('/plug/jquery/plugins/owl-carousel/owl.carousel.min.js');
    }

    public static function maskedInput() {
        SFPage::js('/plug/jquery/plugins/maskedinput/jquery.maskedinput.js', 10);
        SFPage::js('/plug/jquery/plugins/maskedinput/maskedinput.init.js', 10);
    }
    
    public static function numericInput() {
        SFPage::js('/plug/jquery/plugins/numericinput/numericinput.js', 10);
    }

}
