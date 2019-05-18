<?php

class PlugStaticMinify {

    public static function js($file) {
        include_once dirname(__FILE__) . '/magic-min-master/class.magic-min.php';
        $vars = array(
//            'remove_comments' => true
        );
        $minified = new Minifier($vars);
        $minified->minify($file);
    }

    public static function css($file) {
        include_once dirname(__FILE__) . '/magic-min-master/class.magic-min.php';
        $vars = array(
//            'encode' => true,
//            'closure' => true,
//            'remove_comments' => true
        );
        $minified = new Minifier($vars);
        $minified->minify($file);
    }

}
