<?php

class SFPage {

    protected static $menu = '';
    protected static $content = '';
    protected static $positions = array();
    protected static $modules = array();
    protected static $css = array();
    protected static $css_check = array();
    protected static $js = array();
    protected static $js_check = array();
    protected static $seo_title = '';
    protected static $seo_description = '';
    protected static $seo_keywords = '';
    protected static $seo_metatags = '';
    protected static $seo_is = false;
    protected static $meta_raw = '';
    public static $showAside = false;

    public static function init() {
        $com = SFCore::getComponent();

        $q = "
            SELECT t1.item_id, t1.module_id, t1.menu_id, t1.posname, t1.name, t2.class, t1.params, t2.postexec
            FROM module_item t1
            JOIN module t2 ON t2.module_id=t1.module_id and t2.type = 'site'
            WHERE t1.active=1
            ORDER BY t1.npp, t1.item_id
        ";
        $rows = SFDB::assoc($q);
        $curmenu = SFCore::menuCurItem();
        foreach ($rows as $row) {
            if (!$row['postexec'] && (empty($row['menu_id']) || (int) $row['menu_id'] == $curmenu['menu_id'])) {
                $module = new $row['class']($row);
                self::$positions[$row['posname']][] = $module->execute();
            }
        }

        self::$content = $com->execute();
        if (strpos(self::$content, '{') !== false) {
            foreach (self::$positions as $posname => $mcontent) {
                self::$content = str_replace('{position_' . $posname . '}', implode("\n", $mcontent), self::$content);
            }
        }

        foreach ($rows as $row) {
            if ($row['postexec'] && (empty($row['menu_id']) || (int) $row['menu_id'] == $curmenu['menu_id'])) {
                $module = new $row['class']($row);
                self::$positions[$row['posname']][] = $module->execute();
            }
        }
    }

    public static function content() {
        echo self::$content;
    }

    /**
     * 
     * @param string $posname
     * @param string $delimiter (optional = '') разделитель, например: '<hr/>'
     */
    public static function position($posname, $delimiter = '') {
        if ($posname && !empty(self::$positions[$posname])) {
            $i = 0;

            $counter = count(self::$positions[$posname]);
            foreach (self::$positions[$posname] as $mod) {
                echo $mod;
                if ($counter-- > 1) {
                    echo $delimiter;
                }
            }
        }
    }

    public static function module($modname) {
        return false;
    }

    public static function moduleById($module_id) {
        $q = "SELECT t1.item_id, t1.module_id, t1.menu_id, t1.posname, t1.name, t2.class, t1.params
        FROM module_item t1
        JOIN module t2 ON t2.module_id=t1.module_id
        WHERE t1.active=1 AND t1.item_id=" . $module_id;
        if ($row = SFDB::result($q)) {
            $module = new $row['class']($row);
            echo $module->execute();
        }
    }

    public static function css($file, $idx = 100) {
        if (SF_INADMIN) {
            return SFAdminPage::css($file, $idx);
        }
        $idx = (int) $idx;
        if (empty(self::$css[$idx][md5($file)])) {
            if (isset(self::$css_check[md5($file)])) {
                unset(self::$css[self::$css_check[md5($file)]][md5($file)]);
                unset(self::$css_check[md5($file)]);
            }
            self::$css_check[md5($file)] = $idx;
            self::$css[$idx][md5($file)] = $file;
        }
    }

    /**
     * 
     * @param string $file Путь к подключаемому скрипту
     * @param int $idx [optional = 100] Приоритет, 0 - самый высокий, 100 - самый низкий
     * @return void
     */
    public static function js($file, $idx = 100) {
        if (SF_INADMIN) {
            return SFAdminPage::js($file, $idx);
        }
        $idx = (int) $idx;
        if (empty(self::$js[$idx][md5($file)])) {
            if (isset(self::$js_check[md5($file)])) {
                unset(self::$js[self::$js_check[md5($file)]][md5($file)]);
                unset(self::$js_check[md5($file)]);
            }
            self::$js_check[md5($file)] = $idx;
            self::$js[$idx][md5($file)] = $file;
        }
    }

    /**
     * Добавляет JS код в блок подключения скриптов
     * @param string $js inline js code
     * @param int $idx [optional = 100]
     * @return void
     */
    public static function jsInline($js, $idx = 100) {
        if (SF_INADMIN) {
            return false;
        }
        $idx = (int) $idx;
        self::$js[$idx][md5($js)] = ['type' => 'inline', 'value' => $js];
        return true;
    }

    public static function addMetaRaw($str) {
        self::$meta_raw .= "$str\n";
    }

    public static function meta() {
        echo '<title>', str_replace('<br/>', '', htmlspecialchars(self::$seo_title)), self::$seo_title ? ' | ' : '', SFCore::siteParam('site_name'), '</title>', "\r\n";
        echo self::$seo_description ? '<meta name="description" content="' . htmlspecialchars(self::$seo_description) . '" />' . "\r\n" : '';
        echo self::$seo_keywords ? '<meta name="keywords" content="' . htmlspecialchars(self::$seo_keywords) . '" />' . "\r\n" : '';
        echo self::$seo_metatags ? $m->replaceTemplates(self::$seo_metatags) . "\r\n" : '';
        self::metaCSS();

        self::metaJS();

        echo self::$meta_raw;

        echo '<meta charset="utf-8">', "\r\n";

        PlugFrontEnd::output();
    }

    public static function metaCSS() {
        $std = SFCore::siteParam('static_domain');
        $sub = SFCore::siteParam('static_domain_sub') ? 'css.' : '';
        $v = (int) SFCore::siteParam('static_version');
        $hasCache = (bool) SFCore::siteParam('static_cache');
        if ($hasCache && $cacheStr = SFCore::siteParam('static_cache_css')) {
            $cache = array_filter(array_map('trim', explode("\n", $cacheStr)));
            if ($hasCache = (bool) count($cache)) {
                $cache = array_flip($cache);
                self::$css[1][] = "/cache/css/$v.min.css";
            }
        }
        ksort(self::$css);
        foreach (self::$css as $css_arr) {
            foreach ($css_arr as $css) {
                if ($hasCache && isset($cache[trim($css)])) {
                    continue;
                }
                if ($std && strpos($css, 'http') === false) {
                    $css = "http://$sub$std$css";
                }
                echo '<link type="text/css" rel="stylesheet" href="', $css, $v ? '?v=' . $v : '', '" />', "\r\n";
            }
        }
    }

    public static function metaJS() {
        $std = SFCore::siteParam('static_domain');
        $sub = SFCore::siteParam('static_domain_sub') ? 'js.' : '';
        $v = (int) SFCore::siteParam('static_version');
        $hasCache = (bool) SFCore::siteParam('static_cache');
        if ($hasCache && $cacheStr = SFCore::siteParam('static_cache_js')) {
            $cache = array_filter(array_map('trim', explode("\n", $cacheStr)));
            if ($hasCache = (bool) count($cache)) {
                $cache = array_flip($cache);
                self::$js[1][] = "/cache/js/$v.min.js";
            }
        }
        ksort(self::$js);
        foreach (self::$js as $js_arr) {
            foreach ($js_arr as $js) {
                if ('inline' === (string) @$js['type']) {
                    echo '<script type="text/javascript">', $js['value'], '</script>', "\r\n";
                    continue;
                }
                if ($hasCache && isset($cache[trim($js)])) {
                    continue;
                }
                if ($std && strpos($js, 'http') === false) {
                    $js = "http://$sub$std$js";
                }
                echo '<script type="text/javascript" src="', $js, $v && strpos($js, 'http') === false ? '?v=' . $v : '', '"></script>', "\r\n";
            }
        }
    }

    public static function seo($title, $description = '', $keywords = '', $final = false, $metatags = '') {
        if (!self::$seo_is) {
            self::$seo_title = $title;
            self::$seo_description = $description ? $description : self::$seo_description;
            self::$seo_keywords = $keywords ? $keywords : self::$seo_keywords;
            self::$seo_metatags = $metatags ? $metatags : self::$seo_metatags;
            self::$seo_is = $final;
        }
    }

}
