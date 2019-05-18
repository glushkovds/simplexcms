<?php

class SFAdminPage {

    protected static $menu = '';
    protected static $content = '';
    protected static $positions = array();
    protected static $modules = array();
    protected static $css = array();
    protected static $css_check = array();
    protected static $js = array();
    protected static $js_check = array();

    /**
     *
     * @var AdminBase
     */
    protected static $driver = false;

    public static function init() {
        
        $notify = SFAdminNotifyCollection::getInstance();
        if (isset($_GET['sfnotify'])) {
            self::$content = $notify->ajaxData();
            return;
        }

        if ($widgetId = (int) @$_GET['sfwidget']) {
            $q = "SELECT * FROM admin_widget WHERE active = 1 AND widget_id = $widgetId";
            $row = SFDB::result($q);
            $class = $row['class'];
            $widget = new $class($row);
            self::$content = $widget->execute();
            return;
        }

        self::$driver = false;

        $menuCurModel = SFAdminCore::menuCurItem('model');

        $modelNameAr = explode('_', $menuCurModel);
        $extDir = $modelNameAr[0];
        $extDriverName = implode('', $modelNameAr) . '.class.php';
        $extDriverPath = "{$_SERVER['DOCUMENT_ROOT']}/ext/$extDir/admin/$extDriverName";
        $extDriverClass = 'Admin';
        foreach ($modelNameAr as $modelNamePart) {
            $extDriverClass .= ucfirst($modelNamePart);
        }

        if (is_file($extDriverPath)) {
            include_once $extDriverPath;
            if (class_exists($extDriverClass)) {
                self::$driver = new $extDriverClass();
            }
        }

        if (!self::$driver) {
            if (in_array($menuCurModel, array('struct_param', 'struct_data', 'module_param', 'struct_table'))) {
                self::$driver = new AdminStruct();
            } elseif (in_array($menuCurModel, array('module_item'))) {
                include $_SERVER['DOCUMENT_ROOT'] . '/admin/base/moduleitem.class.php';
                self::$driver = new AdminModuleItem();
            } elseif (in_array($menuCurModel, array('component'))) {
                include $_SERVER['DOCUMENT_ROOT'] . '/admin/base/component.class.php';
                self::$driver = new AdminComponent();
            } else {
                self::$driver = new AdminBase();
            }
        }

        self::$content = self::$driver->execute();

        if (!SFAdminCore::$isAjax) {
            $curmenu = SFAdminCore::menuCurItem();
            $q = "
                SELECT t1.item_id, t1.module_id, t1.menu_id, t1.name, 0 is_title, t1.posname, t2.class, t1.params
                FROM module_item t1
                JOIN module t2 using(module_id)
                WHERE t1.active = 1 and t2.type = 'admin'
                ORDER BY t1.npp, t1.item_id
            ";
            $rows = SFDB::assoc($q);
            foreach ($rows as $row) {
                if (empty($row['menu_id']) || (int) $row['menu_id'] == $curmenu['menu_id']) {
                    $mod = new $row['class']($row);
                    self::$positions[$row['posname']][] = $mod->execute();
                }
            }
        }
    }

    public static function content() {
        echo self::$content;
    }

    public static function position($posname) {
        if ($posname && !empty(self::$positions[$posname])) {
            $i = 0;
            foreach (self::$positions[$posname] as $mod_content) {
                if ($mod_content) {
                    if ($i) {
                        echo '<hr class="module-separator" />';
                    }
                    echo $mod_content;
                    $i++;
                }
            }
        }
    }

    public static function module($modname) {
        if ($modname && !empty(self::$modules[$modname])) {
            echo self::$modules[$modname];
        }
    }

    public static function css($file, $idx = 100) {
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

    public static function js($file, $idx = 100) {
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

    public static function meta() {
        //echo $this->description ? '<meta name="description" content="'.$this->description.'" />'."\r\n" : '';
        //echo $this->keywords ? '<meta name="keywords" content="'.$this->keywords.'" />'."\r\n" : '';

        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', "\r\n";

        ksort(self::$css);
        foreach (self::$css as $css_arr) {
            foreach ($css_arr as $css) {
                echo '<link type="text/css" rel="stylesheet" href="', $css, '" />', "\r\n";
            }
        }
        ksort(self::$js);
        foreach (self::$js as $js_arr) {
            foreach ($js_arr as $js) {
                echo '<script type="text/javascript" src="', $js, '"></script>', "\r\n";
            }
        }
    }

    public static function notifications() {
        SFAdminNotifyCollection::getInstance()->content();
    }

}
