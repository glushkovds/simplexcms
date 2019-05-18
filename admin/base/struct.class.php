<?php

class AdminStruct extends AdminBase {

    public function __construct() {
        parent::__construct();
        SFAdminPage::js('/admin/base/js/struct.js');

        $this->actionHandlers['field_param'] = array('method' => 'fieldParam');
        $this->actionHandlers['loadstruct'] = array('method' => 'loadStruct');
    }

    /**
     * Выводим доп. параметры по типу поля
     */
    protected function fieldParam() {
        $fieldId = (int) @$_GET['field_id'];
        $fieldName = SFDB::escape(@$_GET['field_name']);
        $table = SFDB::escape(@$_GET['table']);
        $keyName = SFDB::escape(@$_GET['key_name']);
        $keyValue = (int) @$_GET['key_value'];

        $q = "
            select p.name, p.help, p.label, p.default_value, t.params, f.class, '$this->table' `table`
            from struct_field_param p
            JOIN struct_field f on f.field_id = p.type_id
            LEFT JOIN $table t on $keyName = $keyValue
            where p.field_id = $fieldId
        ";
        $rows = SFDB::assoc($q);

        if (!count($rows)) {
            return;
        }

        $fields = array();
        foreach ($rows as $row) {
            $params = unserialize($row['params']);
            $field = new $row['class']($row);
            $field->form = 'main';
            $field->value = isset($params['main'][$row['name']]) ? $params['main'][$row['name']] : $row['default_value'];
            $fields[] = $field;
        }

        $group = array('label' => 'Доп. параметры');
        $group['fields'] = $fields;
        $portletClass = "field-params-$fieldName";
        include 'tpl/form.portlet.tpl';
    }

    protected function getParams() {
        $params = parent::getParams();
        if (!empty($_POST['field_id'])) {
            $fieldId = (int) $_POST['field_id'];
            $q = "
                select p.name name, p.label label, f.class
                from struct_field_param p
                JOIN struct_field f on f.field_id = p.type_id
                where p.field_id = $fieldId
            ";
            $rows = SFDB::assoc($q);
            foreach ($rows as $row) {
                $field = new $row['class']($row);
                $field->form = 'main';
                $value = $field->getPost(true, 'main');
                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                $params['main'][$field->name] = $value;
            }
        }
//        print_r($params);die;
        return $params;
    }

    protected function loadStruct() {
        $backTo = $_SERVER['HTTP_REFERER'];
        $tableId = (int) @$_GET['table_id'];
        $q = "select * from struct_table where table_id = $tableId";
        $table = SFDB::result($q);
        if (!preg_match('@^[a-z0-9_]+$@', $table['name'])) {
            AdminPlugAlert::error('Таблица не указана', $backTo);
        }
        $q = "SHOW TABLES LIKE '{$table['name']}'";
        if (!SFDB::result($q)) {
            AdminPlugAlert::error('Таблица не найдена', $backTo);
        }

        $f_names = array(
            'active' => 'Активно',
            'user_id' => 'Пользователь',
            'role_id' => 'Роль',
            'priv_id' => 'Привилегия',
            'pid' => 'Родитель',
            'npp' => '№ п/п',
            'name' => 'Название',
            'text' => 'Текст',
            'comment' => 'Комментарий',
            'link' => 'Ссылка',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'pid' => 'PID',
            'state' => 'Статус',
            'label' => 'Ярлык',
            'photo' => 'Изображение',
            'img' => 'Изображение',
            'image' => 'Изображение',
            'file' => 'Файл',
            'phone' => 'Телефон',
            'datetime' => 'Время',
            'coments' => 'Комментарий'
        );

        $f_width = array(
            'active' => '85',
            'user_id' => '140',
            'role_id' => '140',
            'priv_id' => '140',
            'pid' => '0',
            'npp' => '80',
            'name' => '1',
            'text' => '0',
            'comment' => '1',
            'link' => '1',
            'description' => 0,
            'date' => 100,
            'datetime' => 150,
            'state' => 120,
            'label' => 1,
            'photo' => 110,
            'img' => 110,
            'image' => 110,
            'file' => 0,
            'phone' => 180
        );

        $f_filter = array(
            'active' => '1',
            'user_id' => '1',
            'role_id' => '1',
            'priv_id' => '1',
        );

        $q = "select * from struct_field";
        $fieldTypes = SFDB::assoc($q, 'class');

        $q = "SHOW FULL COLUMNS FROM {$table['name']}";
        $rows = SFDB::assoc($q);
//        print_r($rows);
//        die;

        $q = "
            select
              COLUMN_NAME field, REFERENCED_TABLE_NAME `table`, REFERENCED_COLUMN_NAME fk_field
            from INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            where
              TABLE_NAME = '{$table['name']}' AND REFERENCED_TABLE_NAME IS NOT NULL
        ";
        $fkeys = SFDB::assoc($q, 'field');
        $fkeyTitles = array('login', 'name', 'title', 'label');
        foreach ($fkeys as $index => $fkey) {
            $title = '';
            $q = "SHOW COLUMNS FROM {$fkey['table']}";
            $ftFields = SFDB::assoc($q, 'Field');
            foreach ($fkeyTitles as $fkeyTitle) {
                if (isset($ftFields[$fkeyTitle])) {
                    $title = $fkeyTitle;
                }
            }
            if (!$title) {
                foreach ($ftFields as $ftField) {
                    if (strpos($ftField['Type'], 'varchar') === 0) {
                        $title = $ftField['Field'];
                        break;
                    }
                }
            }
            if ($title) {
                $fkeys[$index]['title'] = $title;
            } else {
                unset($fkeys[$index]);
            }
        }

        $success = SFDB::query('start transaction');
        $q = "delete from struct_data where table_id = $tableId";

        $success &= SFDB::query($q);
        foreach ($rows as $i => $field) {
            $isPID = isset($fkeys[$field['Field']]) && $fkeys[$field['Field']]['table'] == $table['name'];

            $insert = array();
            $insert['field_id'] = $fieldTypes['SFFString']['field_id'];
            $insert['label'] = isset($f_names[$field['Field']]) ? $f_names[$field['Field']] : ucfirst($field['Field']);
            if ($isPID) {
                $insert['label'] = $f_names['pid'];
            }

            $params = array(
                'pk' => '0',
                'e2n' => $field['Null'] == 'YES' ? 1 : 0,
                'hidden' => '0',
                'width' => isset($f_width[$field['Field']]) ? $f_width[$field['Field']] : '1',
                'defaultValue' => '',
                'required' => '0',
                'filter' => isset($f_filter[$field['Field']]) ? $f_filter[$field['Field']] : '0'
            );
            
            if($field['Field'] == 'active'){
                $params['defaultValue'] = 1;
            }

            if (isset($fkeys[$field['Field']])) {
                $fkey = $fkeys[$field['Field']];
                $params['is_fk'] = 1;
                $params['fk_table'] = $fkey['table'];
                $params['fk_key'] = $fkey['fk_field'];
                $params['fk_label'] = $fkey['title'];
                $params['fk_is_pid'] = $fkey['table'] == $table['name'];
            }

            if ($isPID) {
                $params['width'] = $f_width['pid'];
            }

            if ($field['Extra'] == 'auto_increment') {
                $insert['label'] = 'ID';
                $params['pk'] = '1';
                $params['e2n'] = '1';
                $params['hidden'] = '1';
                $params['width'] = '60';
                $params['is_fk'] = '';
            }

            if ($field['Type'] == 'int(11)') {
                $insert['field_id'] = $fieldTypes['SFFInt']['field_id'];
            }
            if ($field['Type'] == 'int(1)') {
                $insert['field_id'] = $fieldTypes['SFFBool']['field_id'];
            }
            if ($field['Type'] == 'text') {
                $params['width'] = 0;
                $insert['field_id'] = $fieldTypes['SFFText']['field_id'];
            }
            if (strpos($field['Type'], 'enum') === 0) {
                $insert['field_id'] = $fieldTypes['SFFEnum']['field_id'];
            }
            if ($field['Field'] == 'date') {
                $insert['field_id'] = $fieldTypes['SFFDate']['field_id'];
            }
            if ($field['Field'] == 'datetime') {
                $insert['field_id'] = $fieldTypes['SFFDateTime']['field_id'];
            }
            if ($field['Field'] == 'npp') {
                $insert['field_id'] = $fieldTypes['SFFNPP']['field_id'];
            }
            if ($field['Field'] == 'file') {
                $insert['field_id'] = $fieldTypes['SFFFile']['field_id'];
            }
            if ($field['Field'] == 'photo') {
                $insert['field_id'] = $fieldTypes['SFFImage']['field_id'];
            }

            $insert['params'] = SFDB::escape(serialize(array('main' => $params)));

            $npp = $i + 1;
            if ($field['Field'] == 'npp') {
                $npp = 1;
            }
            $q = "
                INSERT INTO struct_data(npp, table_id, field_id, name, label, help, params)
                VALUES($npp, $tableId, {$insert['field_id']}, '{$field['Field']}', '{$insert['label']}', '{$field['Comment']}', '{$insert['params']}')
            ";
            $success &= $s0 = SFDB::query($q);
            if (!$s0) {
                AdminPlugAlert::error('<b>' . $q . '</b> &mdash; ' . mysql_error());
            }
        }
        SFDB::query($success ? 'commit' : 'rollback');
        if ($success) {
            AdminPlugAlert::success('Структура таблицы <b>' . $table['name'] . '</b> загружена', $backTo);
        } else {

            AdminPlugAlert::error('Структура таблицы <b>' . $table['name'] . '</b> не загружена', $backTo);
        }
    }
    
    public function save() {

        $id = (int) @$_POST[$this->pk->name];
        if ($id) {
            $fieldId = (int) $_POST['field_id'];
            $q = "SELECT * FROM struct_field WHERE field_id = $fieldId";
            $field = SFDB::result($q);

            $sizesRebuild = array();
            if ('SFFImage' == $field['class']) {
                $q = "SELECT * FROM $this->table WHERE {$this->pk->name} = $id";
                $row = SFDB::result($q);
                $oldParams = unserialize($row['params']);
                $oldParams = $oldParams['main'];
                $newParams = $_POST['main'];
                $sizes = array('small', 'medium', 'large');

                foreach ($sizes as $size) {
                    if ($oldParams[$size] != $newParams[$size]) {
                        $sizesRebuild[$size] = $newParams[$size];
                    }
                }
            }

            $ret = parent::save();

            if ($ret && count($sizesRebuild)) {
                $q = "SELECT * FROM struct_table WHERE table_id = {$_POST['table_id']}";
                $table = SFDB::result($q);
                $this->imagesRebuild($table['name'], $_POST['name'], $oldParams['path'], $sizesRebuild);
            }

            return $ret;
        } else {
            return parent::save();
        }
    }

    private function imagesRebuild($table, $fieldName, $dir, $sizesRebuild) {
        include_once "{$_SERVER['DOCUMENT_ROOT']}/core/sffile.class.php";
        $q = "SELECT $fieldName FROM $table";
        $rows = SFDB::assoc($q);
        foreach ($rows as $row) {
            $dir = trim($dir,'/');
            $img = new SFImage("$dir/", $sizesRebuild);
            $img->load("{$_SERVER['DOCUMENT_ROOT']}/uf/images/{$dir}/source/{$row[$fieldName]}", true);
            $img->save();
        }
    }

}
