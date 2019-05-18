<?php
/*
 * Порядок действий:
 * 1. Добавляем модуль в module
 * 2. Добавляем экземпляр модуля в module_item
 * 3. Создаем модель параметров модуля
 */

include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/core/sfdb.class.php';
SFDB::connect();

$modclass = 'ModMap';
$modname  = 'Карта Яндекс';

// Параметры по умолчанию
$params = array(
    'address' => '',
    'hint' =>  '',
    'hash' => '',
    'is_title' => 1,
    'is_wrap' => 0,
    'zoom' => 16,
    'style' => 'height:400px; border:1px solid rgba(0,0,0,0.5);'
);

$report = array();

// 1. Добавляем модуль в module
$q="SELECT COUNT(*) FROM module WHERE class='$modclass'";
if(SFDB::result($q, 0)>0) {
   $report[] = '<span style="color:blue">Модуль уже существует</span>';
} else {
    $q="INSERT INTO module(class, name) VALUES('$modclass', '$modname')";
    SFDB::query($q);
    if(SFDB::errno()) {
        $report[] = '<span style="color:red">Ошибка! Модуль не удалось добавить</span>';
    } else {
        $report[] = '<span style="color:green">Модуль добавлен</span>';
        $module_id = SFDB::insertID();
        
        if($module_id) {
            $counter = 0;
            $counter += SFDB::query("INSERT INTO module_param(module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ($module_id, NULL, 'right', 1, 'address', 'Адрес', 1, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}');") ? 1 : 0;
            $counter += SFDB::query("INSERT INTO module_param(module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ($module_id, NULL, 'right', 1, 'hint', 'Текст', 2, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}');") ? 1 : 0;
            $counter += SFDB::query("INSERT INTO module_param(module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ($module_id, NULL, 'right', 1, 'style', 'Стиль CSS', 3, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}');") ? 1 : 0;
            $counter += SFDB::query("INSERT INTO module_param(module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ($module_id, NULL, 'right', 1, 'hash', 'Хеш', 4, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}');") ? 1 : 0;
            $cnt = 4;
            $report[] = '<span style="color:'.($counter==$cnt ? 'green' : 'red').'">Добавлено параметров: '.$counter.' из '.$cnt.'</span>';
        }
    }
}

// 2. Добавляем экземпляр модуля в module_item
//$module_id = (int)SFDB::result("SELECT module_id FROM module WHERE class='$modclass'", 0);
//$menu_id = (int)SFDB::result("SELECT menu_id FROM menu WHERE link='/contacts/'", 0);
//$menu_id = $menu_id>0 ? $menu_id : 'NULL';
//$q="SELECT COUNT(*) FROM module_item WHERE module_id=$module_id";
//if(SFDB::result($q, 0)>0) {
//   $report[] = '<span style="color:blue">Экземпляр модуля уже существует';
//} else {
//    $q="INSERT INTO module_item(module_id, menu_id, posname, active, npp, name, params)
//        VALUES($module_id, $menu_id, 'content-after', 1, 0, 'Мы на карте', '".SFDB::escape(serialize($params))."')";
//    SFDB::query($q);
//    if(SFDB::errno()) {
//        $report[] = '<span style="color:red">Ошибка! Экземпляр модуля не удалось добавить';
//    } else {
//        $report[] = '<span style="color:green">Экземпляр модуля добавлен';
//    }
//}
foreach($report as $msg) {
    echo $msg, '<br />';
}