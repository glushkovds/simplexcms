<?php

class AdminModuleItem extends AdminBase {

    public function __construct() {
        parent::__construct();
        SFAdminPage::js('/admin/base/js/moduleitem.js');
        $this->actionHandlers['module_param'] = array('method' => 'moduleParam');
    }

    protected function moduleParam() {
        $moduleId = (int) @$_GET['module_id'];
        $itemId = (int) @$_GET['item_id'];

        $q = "SELECT * FROM module WHERE module_id = $moduleId";
        $module = SFDB::result($q);

        $menuTable = $module['type'] == 'site' ? 'menu' : 'admin_menu';
        $q = "SELECT menu_id, menu_pid pid, name FROM $menuTable ORDER BY npp, menu_id";
        $menuItems = SFDB::assoc($q);

        $q = "
            select mp_id, param_pid, p.name, p.help, p.label, p.position, p.params params, t.params table_params, f.class, '$this->table' `table`
            from module_param p
            LEFT JOIN struct_field f using(field_id)
            LEFT JOIN module_item t on item_id = $itemId
            WHERE p.module_id = $moduleId
            ORDER BY p.npp
        ";
        $rows = SFDB::assoc($q, 'param_pid', 'mp_id');
        if (count($rows)) {

            $positions = array('left' => '', 'right' => '');
            $editorInited = false;

            $hasWithoutGroup = false;
            foreach ($rows[''] as $pid => $row) {
                if (!isset($rows[$pid]) && $row['class']) {
                    $hasWithoutGroup = true;
                    break;
                }
            }

            if ($hasWithoutGroup) {
                foreach ($rows[''] as $row) {
                    if ($row['class']) {
                        $params = unserialize($row['table_params']);
                        $field = new $row['class']($row);
                        $field->value = isset($params[$row['name']]) ? $params[$row['name']] : $field->defaultValue;
                        $fields[] = $field;
                    }
                }
                $group = array('label' => 'Параметры');
                $group['fields'] = $fields;
                $portletClass = "field-params-nogroup";
                ob_start();
                if (!empty($field->params['editor_full']) && !$editorInited) {
                    PlugEditor::tinymce('full');
                    $editorInited = true;
                }
                include 'tpl/form.portlet.tpl';
                $positions['right'] .= ob_get_clean();
            }

            foreach ($rows[''] as $pid => $row0) {
                $fields = array();
                if (!isset($rows[$pid])) {
                    continue;
                }
                foreach ($rows[$pid] as $row) {
                    $params = unserialize($row['table_params']);
                    $field = new $row['class']($row);
                    $field->value = isset($params[$row['name']]) ? $params[$row['name']] : $field->defaultValue;
                    $fields[] = $field;
                }

                $group = array('label' => $row0['label']);
                $group['fields'] = $fields;
                $portletClass = "field-params-{$row0['name']}";
                ob_start();
                if (!empty($field->params['editor_full']) && !$editorInited) {
                    PlugEditor::tinymce('full');
                    $editorInited = true;
                }
                include 'tpl/form.portlet.tpl';
                $positions[$row0['position']] .= ob_get_clean();
            }
        }

        $menuOptions = array('<option value=""></option>');
        foreach ($menuItems as $mitem) {
            $span = $mitem['pid'] ? str_repeat('&nbsp;', 4) : '';
            $menuOptions[] = '<option value="' . $mitem['menu_id'] . '">' . $span . htmlspecialchars($mitem['name']) . '</option>';
        }
        $positions['menu_items'] = implode("\n", $menuOptions);

        echo json_encode($positions);
    }

    protected function getParams() {
        $params = parent::getParams();
        if (!empty($_POST['module_id'])) {
            $moduleId = (int) $_POST['module_id'];
            $q = "
                select p.name name, p.label label, f.class
                from module_param p
                JOIN struct_field f using(field_id)
                WHERE p.module_id = $moduleId
                ORDER BY p.npp
            ";
            $rows = SFDB::assoc($q);
            foreach ($rows as $row) {
                $field = new $row['class']($row);
                $value = $field->getPost(true);
                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                $params[$field->name] = $value;
            }
        }
        return $params;
    }

}
