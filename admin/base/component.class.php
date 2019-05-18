<?php

/**
 * Отвечает за редактирование компонента в админке
 */
class AdminComponent extends AdminBase {

    public function __construct() {
        parent::__construct();
    }

    protected function tableParamsInit() {
        parent::tableParamsInit();
        $componentId = (int) @$_GET['component_id'];
        if (!$componentId) {
            return;
        }

        $q = "
            select cp_id, param_pid, p.name, p.help, p.label, p.position, p.params params, t.params table_params, f.class, '$this->table' `table`
            from component_param p
            LEFT JOIN struct_field f using(field_id)
            LEFT JOIN component t on t.component_id = $componentId
            where p.component_id = $componentId
        ";
        $rows = SFDB::assoc($q, 'param_pid', 'cp_id');
        if (!count($rows)) {
            return;
        }

        $hasWithoutGroup = false;
        foreach ($rows[''] as $pid => $row) {
            if (!isset($rows[$pid]) && $row['class']) {
                $hasWithoutGroup = true;
                break;
            }
        }

        if ($hasWithoutGroup) {
            $fields = array();
            foreach ($rows[''] as $row) {
                if ($row['class']) {
                    $params = unserialize($row['table_params']);
                    $field = new $row['class']($row);
                    $field->value = isset($params[$row['name']]) ? $params[$row['name']] : $field->defaultValue;
                    $fields[] = $field;
                }
            }
            $group = array('name' => '', 'label' => 'Параметры');
            $group['fields'] = $fields;
            $this->params['right'][-1] = $group;
        }

        $counter = -2;
        foreach ($rows[''] as $pid => $row0) {
            $fields = array();
            if (!isset($rows[$pid])) {
                continue;
            }
            $group = $row0;
            foreach ($rows[$pid] as $row) {
                $params = unserialize($row['table_params']);
                $field = new $row['class']($row);
                $field->form = $row0['name'];
                $field->value = isset($params[$row['name']]) ? $params[$row['name']] : $field->defaultValue;
                $fields[] = $field;
            }
            $group['fields'] = $fields;
            $this->params['right'][$counter --] = $group;
        }
    }

    protected function getParams() {
        $params = parent::getParams();
        if (!empty($_POST['component_id'])) {
            $componentId = (int) $_POST['component_id'];
            $q = "
                select cp_id, param_pid, p.name name, p.label label, f.class
                from component_param p
                LEFT JOIN struct_field f using(field_id)
                where p.component_id = $componentId
            ";
            $rows = SFDB::assoc($q);
            foreach ($rows as $row) {
                if (!$row['class']) {
                    continue;
                }
                $field = new $row['class']($row);
                if ($row['param_pid']) {
                    $groupName = '';
                    foreach ($rows as $row1) {
                        if($row1['cp_id'] == $row['param_pid']){
                            $groupName = $row1['name'];
                            break;
                        }
                    }
                    $value = $field->getPost(true, $groupName);
                    $value = preg_replace("@^'(.+)'$@", '$1', $value);
                    $params[$groupName][$field->name] = $value;
                } else {
                    $value = $field->getPost(true);
                    $value = preg_replace("@^'(.+)'$@", '$1', $value);
                    $params[$field->name] = $value;
                }
            }
        }
        return $params;
    }

}
