<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SFCore {

    private static $ajax = false;
    private static $uri = array();
    private static $path = '';
    private static $site_params = false;
    private static $menu_tree = array();
    private static $menu_by_id = array();
    private static $menu_by_link = array();
    private static $menu_by_ext = array();
    private static $menu_cur = false;
    private static $component_level = 0;
    private static $component_menu_id = 0;
    private static $content_only = false;

    private function __construct() {
        
    }

    public static function init() {
        $url_info = parse_url($_SERVER['REQUEST_URI']);
        self::$path = $url_info['path'];
        self::$uri = array_slice(explode('/', self::$path), 1);
        SFDB::bind(array('SITE_PATH' => self::$path, 'SITE_LINK' => $url_info['path'] . (isset($url_info['query']) ? '?' . $url_info['query'] : '')));
        
        if(!empty($_REQUEST['sf_plug_name'])){
            $plug = 'Plug'.ucfirst(SFDB::escape($_REQUEST['sf_plug_name']));
            $method = empty($_REQUEST['sf_plug_method']) ? 'execute' : SFDB::escape($_REQUEST['sf_plug_method']);
            $plug::$method();
            exit;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            self::$ajax = true;
            self::$content_only = true;
            $sf_module_id = isset($_REQUEST['sf_module_id']) ? (int) $_REQUEST['sf_module_id'] : 0;
            if ($sf_module_id > 0) {
                SFPage::moduleById($sf_module_id);
                exit();
            }
        } else {
            $q = "SELECT title, description, keywords, metatags FROM seo WHERE link=@SITE_LINK";
            if ($row = SFDB::result($q)) {
                SFPage::seo($row['title'], $row['description'], $row['keywords'], true, $row['metatags']);
            }
        }

        $q = "SELECT alias, value FROM settings";
        $rows = SFDB::assoc($q);
        foreach ($rows as $row) {
            self::$site_params[$row['alias']] = $row['value'];
        }

        $q = "SELECT t1.menu_id, t1.menu_pid, t1.hidden, t1.component_id, t1.name, t1.link,
               t2.class
        FROM menu t1
        LEFT JOIN component t2 ON t2.component_id=t1.component_id
        WHERE t1.active=1
        ORDER BY t1.npp";
        $rows = SFDB::assoc($q);
        foreach ($rows as $row) {
            self::$menu_tree[(int) $row['menu_pid']][(int) $row['menu_id']] = $row;
            self::$menu_by_id[(int) $row['menu_id']] = $row;
            self::$menu_by_link[md5($row['link'])] = $row;
            self::$menu_by_ext[strtolower(substr($row['class'], 3))][] = $row;
            if ($row['link'] == self::$path && !self::$menu_cur) {
                self::$menu_cur = $row;
            }
        }
    }

    public static function ajax() {
        return self::$ajax;
    }

    public static function uri($i = false) {
        if ($i === false) {
            return self::$uri;
        }
        $i+= self::$component_level;
        return isset(self::$uri[$i]) ? self::$uri[$i] : '';
    }

    public static function uri_r($i) {
        $i = count(self::$uri) - 2 - $i;
        ;
        return isset(self::$uri[$i]) ? self::$uri[$i] : '';
    }

    public static function path($beg = 0, $len = 0) {
        $path = self::$path;

        $beg = $beg < 0 ? count(self::$uri) + $beg : $beg;
        if ($beg + $len > 0) {
            $path = $len ? join('/', array_slice(self::$uri, $beg, $len)) : join('/', array_slice(self::$uri, $beg));
            $path = str_replace('//', '/', '/' . $path . '/');
        }
        return $path ? $path : '/';
    }

    public static function siteParam($key = false) {

        if(!self::$site_params) {
            $q = "SELECT alias, value FROM settings";
            $rows = SFDB::assoc($q);
            foreach ($rows as $row) {
                self::$site_params[$row['alias']] = $row['value'];
            }
        }

        if ($key === false) {
            return self::$site_params;
        }
        if (!isset(self::$site_params[$key])) {
            self::$site_params[$key] = '';
            $q = "INSERT INTO settings(name, alias, value) VALUES('Новый параметр')";
        }
        return isset(self::$site_params[$key]) ? self::$site_params[$key] : '';
    }

    public static function getComponent() {
        $class = $classDefault = SFConfig::$component_default;
        $path = '/';
        $i = 0;
        if (!empty(self::$menu_by_link[md5($path)]['class'])) {
            $class = self::$menu_by_link[md5($path)]['class'];
            self::$component_level = $i;
            self::$component_menu_id = self::$menu_by_link[md5($path)]['menu_id'];
        }
        $i++;
        foreach (self::$uri as $u) {
            if ($u) {
                $path.=$u . '/';
                if (!empty(self::$menu_by_link[md5($path)])) {
                    $class = self::$menu_by_link[md5($path)]['class'] ?: $classDefault;
                    self::$component_level = $i;
                    self::$component_menu_id = self::$menu_by_link[md5($path)]['menu_id'];
                }
            }
            $i++;
        }
        return new $class();
    }

    public static function execute() {
        if (self::$content_only) {
            SFPage::content();
        } else {
            if (isset($_REQUEST['print']) && is_file('theme/' . SFConfig::$theme . '/print.tpl')) {
                include 'theme/' . SFConfig::$theme . '/print.tpl';
                return;
            }
            if (is_file('theme/' . SFConfig::$theme . '/index.tpl')) {
                include 'theme/' . SFConfig::$theme . '/index.tpl';
            }
        }
    }

    public static function menu($type = 'tree') {
        switch ($type) {
            case 'by_id' : return self::$menu_by_id;
            case 'by_link' : return self::$menu_by_link;
            case 'by_ext' : return self::$menu_by_ext;
        }
        return self::$menu_tree;
    }

    public static function menuCurItem() {
        return self::$menu_cur;
    }

    public static function componentPath() {
        return self::path(self::$component_level);
    }

    public static function componentLevel() {
        return self::$component_level;
    }

    public static function componentMenuID() {
        return self::$component_menu_id;
    }

    public static function componentTitle() {
        return self::$menu_by_id[self::$component_menu_id]['name'];
    }

    public static function error404() {
        header("HTTP/1.0 404 Not Found");
        SFPage::seo('Error 404');
        if (is_file('theme/' . SFConfig::$theme . '/404.tpl')) {
            self::$content_only = true;
            include 'theme/' . SFConfig::$theme . '/404.tpl';
        } else {
            echo self::siteParam('error404');
        }
    }
    
    /**
     * 
     * @return bool
     */
    public static function isHttps() {
        return !empty($_SERVER['HTTPS']);
    }

    /**
     * 
     * @param bool $withSlashDots [optional = false] ://
     * @return string https / http
     */
    public static function httpProtocol($withSlashDots = false) {
        return (self::isHttps() ? 'https' : 'http') . ($withSlashDots ? '://' : '');
    }

}
