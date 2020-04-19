<?php

class AdminBase {

    public $name = '';
    protected $action = '';
    protected $tableId = 0;
    protected $table = '';
    protected $tableData = array();  // struct_table row
    protected $fields = array();     // fields by name
    protected $fks = array();        // forieng key fields
    /**
     *
     * @var SFFInt
     */
    protected $pk = false;
    protected $pid = false;
    protected $isHierarchy = false;
    protected $params = array('left' => array(), 'right' => array());
    protected $where = array();
    protected $where_sys = array();
    protected $order = '';
    protected $order2 = '';
    protected $desc = 0;
    protected $errors = array();
    protected $p = 0;
    protected $p_on = 50;
    protected $p_count = 0;
    protected $is_filter = false;
    static protected $is_init = false;
    protected $actionHandlers = array(
        '' => array('method' => 'show'),
        'form' => array('method' => 'form'),
        'save' => array('method' => 'save'),
        'delete' => array('method' => 'delete'),
        'validate' => array('method' => 'validate'),
        'bool' => array('method' => 'bool'),
        'change_enum' => array('method' => 'changeENUM'),
        'change_npp' => array('method' => 'changeNPP'),
        'delete_field' => array('method' => 'deleteField')
    );
    protected $row = false;
    public static $currentWhere = '';
    public static $isGroup = false;
    public static $isAdd = false;
    public static $isEdit = false;
    protected $canAdd = false;
    protected $canCopy = false;
    protected $canEdit = false;
    protected $canEditGroup = false;
    protected $canDelete = false;
    public $title = '';

    /**
     * Количество кнопок у каждой строки в списке записей (метод show). Влияет на ширину колонки
     * Необязательно задавать, пересчитывается на JS
     */
    protected $rowActionsCnt = 1;

    /**
     *
     * @var string ID записей при групповом редактировании (через запятую)
     */
    public $ids;

    public function __construct() {
        $this->title = SFAdminCore::menuCurItem('name');
        $this->ids = isset($_GET['ids']) ? $_GET['ids'] : '';
        if (isset($_POST['group_ids'])) {
            $this->ids = $_POST['group_ids'];
        }

        $this->p = & $_SESSION[$this->table]['p'];
        if (!isset($this->p)) {
            $this->p = 0;
        }

        $this->initTableName();
        $this->initTableData();

        $this->canAdd = $this->tableData['priv_add'] ? SFUser::ican((int) $this->tableData['priv_add']) : true;
        $this->canCopy = $this->canAdd;
        $this->canEdit = $this->tableData['priv_edit'] ? SFUser::ican((int) $this->tableData['priv_edit']) : true;
        $this->canEditGroup = $this->canEdit;
        $this->canDelete = $this->tableData['priv_delete'] ? SFUser::ican((int) $this->tableData['priv_delete']) : true;
    }

    protected function initTableName() {
        if (empty($this->table)) {
            $row = SFAdminCore::menuCurItem();
            if ($row['model']) {
                $this->table = $row['model'];
            }
        }
        $this->name = $this->table;
    }

    protected function initTableData() {
        $q = "SELECT * FROM struct_table WHERE name = '$this->table'";
        $this->tableData = SFDB::result($q);
        $this->tableId = $this->tableData['table_id'];
    }

    protected function deleteField() {
        $fieldName = SFDB::escape(@$_GET['field_name']);
        $keyName = SFDB::escape(@$_GET['key_name']);
        $keyValue = (int) @$_GET['key_value'];
        $field = $this->fields[$fieldName];
        $q = "select $field->name from $this->table where $keyName = $keyValue";
        $row = SFDB::result($q);
        $success = $field->delete($row[$field->name]);
        if ($success) {
            $q = "UPDATE " . $this->table . " SET " . $field->name . "='' WHERE $keyName = $keyValue";
            $success = SFDB::query($q);
        }
        echo json_encode(array('success' => $success));
    }

    public function content() {
        $row = SFAdminCore::menuCurItem();

        if ($row['model']) {

            $this->initTable();

            $action = isset($_GET['action']) ? $_GET['action'] : '';

            if ($action == 'save') {
                if ($keyValue = $this->save()) {
                    if (isset($_REQUEST['submit_apply'])) {
                        $href = './?action=form&' . $this->pk->name . '=' . $keyValue;
                        header('location: ' . $href);
                    } elseif (isset($_REQUEST['submit_save_add'])) {
                        header('location: ./?action=form');
                    } else {
                        header("location: ./?save_id=$keyValue");
                    }
                    exit();
                }
            } else {
                if (isset($this->actionHandlers[$action])) {
                    $method = $this->actionHandlers[$action]['method'];
                    if (method_exists($this, $method)) {
                        $this->$method();
                    } else {
                        echo SFAdminCore::siteParam('error404');
                    }
                } elseif (method_exists($this, $action)) {
                    $this->$action();
                } else {
                    echo SFAdminCore::siteParam('error404');
                }
            }
        } else {
            $this->widgets();
            $this->index();
        }
    }

    protected function widgets() {
        $q = "SELECT * FROM admin_widget WHERE active = 1 ORDER BY npp, widget_id";
        $rows = SFDB::assoc($q);
        echo count($rows) ? '<div class="widgets">' : '';
        foreach ($rows as $row) {
            if (!$row['priv_id'] || SFUser::ican((int) $row['priv_id'])) {
                $class = $row['class'];
                $widget = new $class($row);
                echo $widget->execute();
            }
        }
        echo count($rows) ? '</div>' : '';
        echo '<div class="clearfix"></div>' . "\n";
    }

    protected function changeENUM($withRedirect = true) {
        $field = SFDB::escape($_REQUEST['field']);
        $new = SFDB::escape($_REQUEST['newstatus']);
        $new = $new ? "'$new'" : 'null';

        if (!$this->canEdit) {
            AdminPlugAlert::error("В этом разделе запрещено редактировать записи", './');
        }

        $q = "SELECT * FROM struct_data WHERE table_id = {$this->tableData['table_id']} AND name = '$field'";
        $fieldDB = SFDB::result($q);
        $fieldParams = unserialize($fieldDB['params']);
        if (@$fieldParams['main']['readonly']) {
            AdminPlugAlert::error("Поле <b>{$fieldDB['label']}</b> только для чтения!", './');
        }

        $id = (int) $_REQUEST[$this->pk->name];
        $q = "UPDATE `$this->table` SET `$field` = $new WHERE {$this->pk->name} = $id";
        SFDB::query($q);
        if ($withRedirect) {
            header('location: ./');
            exit;
        }
    }

    protected function changeNPP() {
        $field = SFDB::escape($_REQUEST['field']);

        $q = "SELECT * FROM struct_data WHERE table_id = {$this->tableData['table_id']} AND name = '$field'";
        $fieldDB = SFDB::result($q);
        $fieldParams = unserialize($fieldDB['params']);
        if (@$fieldParams['main']['readonly']) {
            AdminPlugAlert::error("Поле <b>{$fieldDB['label']}</b> только для чтения!", './');
        }

        $inc = isset($_GET['up']) ? '-1' : '+1';
        $id = (int) $_REQUEST[$this->pk->name];
        $q = "UPDATE `$this->table` SET `$field` = `$field` $inc WHERE {$this->pk->name} = $id";
        SFDB::query($q);
        header('location: ./');
        exit;
    }

    private function index() {
        $cur = SFAdminCore::menuCurItem();
        $cur_id = isset($cur['menu_id']) ? $cur['menu_id'] : 0;
        $menu = SFAdminCore::menu();
        if (isset($menu[$cur_id])) {
            include dirname(__FILE__) . '/tpl/index.tpl';
        }
    }

    public final function execute($is_print = false) {
        if ($is_print) {
            $this->content();
        } else {
            ob_start();
            $this->content();
            return ob_get_clean();
        }
    }

    protected function getSelectSelect() {
        $ar = array();
        foreach ($this->fks as $field) {
            $fkAdd = '' . $field->fk->table . $field->name . '.' . $field->fk->label . ' ' . $field->name . '_label';
            if ($field->fk->table == $this->table) {
                $fkAdd = '(SELECT ' . $field->fk->label . ' FROM ' . $field->fk->table . ' t_' . $this->table . ' WHERE ' . $this->pk->name . ' = ' . $this->table . '.' . $field->name . ') ' . $field->name . '_label';
            }
            $ar[] = '' . $this->table . '.' . $field->name;
            $ar[] = $fkAdd;
        }
        foreach ($this->fields as $field) {
            if ($field->isVisible && !isset($this->fks[$field->name])) {
                $selectField = $field->selectQueryField();
                if ($selectField) {
                    $ar[] = $selectField;
                }
            }
        }
        $ret = "SELECT " . join(', ', $ar);
//        echo $ret;
        return $ret;
    }

    protected function getSelectFrom() {
        $q = "\r\nFROM " . $this->table;
        foreach ($this->fks as $field) {
            if ($field->fk->table != $this->table) {
                $q .= "\r\n" . ($field->isnull ? "LEFT " : "") . "JOIN " . $field->fk->table . " " . $field->fk->table . $field->name . " ON " . $field->fk->table . $field->name . "." . $field->fk->key . "=" . $this->table . "." . $field->name;
            }
        }
        return $q;
    }

    protected function getSelectWhere() {
        $where = array_merge($this->where_sys, $this->where);
        $ret = count($where) ? "\r\nWHERE " . join(' AND ', $where) : '';
        self::$currentWhere = $ret;
        return $ret;
    }

    protected function getSelectOrderBy() {
        $order = array();
        if ($this->order) {
            $order[] = $this->order . ($this->desc ? ' DESC' : '');
        }
        if ($this->order2) {
            $order[] = $this->order2;
        }
        return count($order) ? "\r\nORDER BY " . join(', ', $order) : "";
    }

    protected function getSelectLimit() {
        return $this->isHierarchy ? "" : "\r\nLIMIT " . $this->p * $this->p_on . ", " . $this->p_on;
    }

    protected function getQueryCount() {
        $q = "SELECT COUNT(*) cnt";
        $q .= $this->getSelectFrom();
        $q .= $this->getSelectWhere();
        return $q;
    }

    protected function getQuerySelect() {
        $q = $this->getSelectSelect();
        $q .= $this->getSelectFrom();
        $q .= $this->getSelectWhere();
        $q .= $this->getSelectOrderBy();
        $q .= $this->getSelectLimit();
        return $q;
    }

    protected function prepareWhere() {
        foreach ($this->fields as $field) {
            $_SESSION[$this->table]['filter'][$field->name] = isset($_SESSION[$this->table]['filter'][$field->name]) ? $_SESSION[$this->table]['filter'][$field->name] : '';
            $_SESSION[$this->table]['filter'][$field->name] = isset($_REQUEST['filter'][$field->name]) ? $_REQUEST['filter'][$field->name] : $_SESSION[$this->table]['filter'][$field->name];
            if ($_SESSION[$this->table]['filter'][$field->name] !== '') {
                if ($_SESSION[$this->table]['filter'][$field->name] === 'null') {
                    $this->where[] = '' . $this->table . '.' . $field->name . " IS NULL";
//                } elseif (!($this->pid && $field->name == $this->pid->name)) {
                } else {
                    $this->isHierarchy = false;
                    if ($field instanceof SFFDateTime) {
                        $this->where[] = '' . $this->table . '.' . $field->name . " like '" . PlugTime::mysql($_SESSION[$this->table]['filter'][$field->name]) . "%'";
                    } else {
                        $fval = SFDB::escape($_SESSION[$this->table]['filter'][$field->name]);
                        $this->where[] = "$this->table.$field->name " . (strpos($fval, '%') === false ? '=' : 'like') . " '$fval'";
                    }
                }
            }
        }
    }

    public function show() {

        $this->prepareWhere();

        $q = $this->getQueryCount();
        $cnt = SFDB::result($q, 'cnt');
        if ($cnt > 200 && $this->isHierarchy) {
            $this->isHierarchy = false;
            if ($_SESSION[$this->table]['filter'][$this->pid->name] !== '') {
                $this->isHierarchy = false;
                $this->where[] = '' . $this->table . '.' . $this->pid->name . "='" . SFDB::escape($_SESSION[$this->table]['filter'][$this->pid->name]) . "'";
                $q = $this->getQueryCount();
                $cnt = SFDB::result($q, 'cnt');
            }
        }

        $this->p_on = $this->isHierarchy ? 200 : $this->p_on;
        $pagecontrol = new AdminPlugPageControl($this->p, $this->p_on, $cnt);

        $q = $this->getQuerySelect();
//        echo $q;
        $r = SFDB::query($q);
        $rows = array();
        if ($this->isHierarchy) {
            $tree_name = $this->pid->name;
            foreach ($this->fields as $field) {
                if ($field->isVisible && $field instanceof SFFString) {
                    $tree_name = $field->name;
                    break;
                }
            }

            $tree = array();
            while ($row = SFDB::fetch($r)) {
                $tree[(int) $row[$this->pid->name]][(int) $row[$this->pk->name]] = $row;
            }
            $id_start = isset($_SESSION[$this->table]['filter'][$this->pid->name]) ? (int) $_SESSION[$this->table]['filter'][$this->pid->name] : 0;
            $list = SFService::tree2list($tree, $id_start);
//            print_r($tree);
//            print_r($list);
            foreach ($list as $l) {
                if ($l['tree_level']) {
                    $l[$tree_name] = '<div style="padding-left:' . (25 * $l['tree_level']) . 'px">' . $l[$tree_name] . '</div>';
                }
                $rows[] = $l;
            }
        } else {
            while ($row = SFDB::fetch($r)) {
                $rows[] = $row;
            }
        }

        $saveId = (int) @$_GET['save_id'];

        include dirname(__FILE__) . '/tpl/show.tpl';
    }

    /**
     * 
     * @param SFField $field
     * @param array $row
     * @return void Use echo
     */
    protected function showCell($field, $row) {
        echo $field->show($row);
    }

    protected function initTable() {

        if ($this->tableId) {

            // TABLE STRUCTURE
            $q = "
                SELECT  t1.*, t2.class, '$this->table' `table`
                FROM struct_data t1
                JOIN struct_field t2 USING(field_id)
                WHERE t1.table_id=$this->tableId
                ORDER BY t1.npp, t1.id
            ";
            $fields = SFDB::assoc($q);
            $pkName = '';
            foreach ($fields as $field) {
//                echo $field['params'];
                $field['params'] = unserialize($field['params']);
                if (!empty($field['params']['main']['filter'])) {
                    $this->is_filter = true;
                }
                if (!empty($field['params']['main']['pk'])) {
                    $pkName = $field['name'];
                }
//                print_r($field['params']['main']);
                $this->addField(new $field['class']($field));
            }

            foreach ($this->fields as $field) {
                $field->tablePk = $pkName;
            }

            //TABLE PARAMS
            $this->tableParamsInit();
        }

        $this->action = isset($_GET['action']) ? $_GET['action'] : '';
        isset($_GET['p']) ? $this->p = abs($_GET['p']) : null;

        // ORDER SETTINGS
        if (!self::$is_init) {
            self::$is_init = true;
            if (isset($_REQUEST['o']) && isset($this->fields[$_REQUEST['o']])) {
                $o = $_REQUEST['o'];
                if (isset($_SESSION[$this->table]['order_by']) && $_SESSION[$this->table]['order_by'] == $o) {
                    $_SESSION[$this->table]['order_desc'] = ($_SESSION[$this->table]['order_desc'] + 1) % 2;
                } else {
                    $_SESSION[$this->table]['order_by'] = $o;
                    $_SESSION[$this->table]['order_desc'] = 0;
                }
            }
            $this->order = isset($_SESSION[$this->table]['order_by']) ? $_SESSION[$this->table]['order_by'] : $this->order;
            $this->desc = isset($_SESSION[$this->table]['order_desc']) ? $_SESSION[$this->table]['order_desc'] : $this->desc;
            if (empty($this->order)) {
                if ($this->tableData['order_by']) {
                    $this->order = $this->tableData['order_by'];
                    $this->desc = (int) $this->tableData['order_desc'];
                } else {
                    $this->order = $this->order2;
                    $this->order2 = '';
                }
            }
            $_SESSION[$this->table]['order_by'] = $this->order;
            $_SESSION[$this->table]['order_desc'] = $this->desc;
        }
    }

    /**
     * Загрузка параметров из БД
     * @return array
     */
    protected function tableParamsLoad() {
        $q = "
            SELECT param_id, param_pid, pos, t1.name, t1.label, t1.params, t2.class, '$this->table' `table`
            FROM struct_param t1
            LEFT JOIN struct_field t2 USING(field_id)
            WHERE table_id = $this->tableId
        ";
        $params = SFDB::assoc($q, 'param_pid', 'param_id');
        return $params;
    }

    /**
     * Инициализация параметров. Загружаются из $this->tableParamsLoad()
     * @return void
     */
    protected function tableParamsInit() {
        $params = $this->tableParamsLoad();
        if (count($params)) {
            foreach ($params[''] as $group_id => $group) {
                $this->params[$group['pos']][$group_id] = $group;
                $this->params[$group['pos']][$group_id]['fields'] = array();

                if (isset($params[$group_id])) {
                    foreach ($params[$group_id] as $param) {
                        $field = new $param['class']($param);
                        $field->form = $group['name'];
                        $this->params[$group['pos']][$group_id]['fields'][$param['name']] = $field;
                    }
                } elseif ($group['class']) {
                    $field = new $group['class']($group);
                    $this->params[$group['pos']][$group_id]['field'] = $field;
                }
            }
        }
//        print_r($this->params);
    }

    protected function filter() {
        if ($this->is_filter) {
            include dirname(__FILE__) . '/tpl/filter.tpl';
        }
    }

    protected function filterExtra($addClearfix = true) {
        include 'tpl/filter.extra.tpl';
    }

    public function addField($field) {
        if ($field instanceof SFField) {
            $this->fields[$field->name] = &$field;

            if ($field->pk) {
                $this->pk = &$field;
                $this->order2 = $field->name;
            }
            if ($field->fk) {
                $this->fks[$field->name] = &$field;
                if ($field->fk->table == $this->table) {
                    $this->pid = &$field;
                    $this->isHierarchy = true;
                }
            }

            /* SYSTEM FILTER */
            if ($field->name == 'priv_id' && !SFUser::ican('dev')) {
                if ($field->isnull) {
                    $this->where_sys[] = "(" . $this->table . ".priv_id IN(" . join(',', SFUser::privIds()) . ") OR " . $this->table . ".priv_id IS NULL)";
                } else {
                    $this->where_sys[] = "" . $this->table . ".priv_id IN(" . join(',', SFUser::privIds()) . ")";
                }
            }
            if ($field->name == 'role_id' && !SFUser::ican('dev')) {
                $this->where_sys[] = "" . $this->table . ".role_id IN(SELECT role_id FROM user_role WHERE priv_id IN(" . join(',', SFUser::privIds()) . "))";
            }
        }
    }

    public function boolChange() {
        $pk = (int) $_REQUEST['pk'];
        $field = $_REQUEST['field'];
        if ($pk && isset($this->fields[$field])) {

            $q = "SELECT * FROM struct_data WHERE table_id = {$this->tableData['table_id']} AND name = '$field'";
            $fieldDB = SFDB::result($q);
            $fieldParams = unserialize($fieldDB['params']);
            if (!@$fieldParams['main']['readonly']) {
                $q = "UPDATE " . $this->table . " SET $field=($field+1)%2 WHERE " . $this->pk->name . "=" . $pk;
                SFDB::query($q);
            }

            $q = "SELECT $field val FROM " . $this->table . " WHERE " . $this->pk->name . "=" . $pk;
            $row = SFDB::result($q);
            if (isset($row['val'])) {
                echo $row['val'] ? 'Да' : 'Нет';
                return;
            }
        }
        echo 'error';
    }

    public function form() {

        if (isset($_GET['ids']) && count(explode(',', $_GET['ids'])) == 1) {
            header("location: ?action=form&{$this->pk->name}={$_GET['ids']}");
            exit;
        }

        $where = $this->where_sys;

        $title = 'Добавить запись';

        $isGroup = self::$isGroup = !empty($_GET['ids']);
        $isAdd = self::$isAdd = !$isGroup && empty($_REQUEST[$this->pk->name]);
        $isEdit = self::$isEdit = !$isGroup && !$isAdd;

        if ($isAdd && !$this->canAdd) {
            AdminPlugAlert::error('Ошибка! Недостаточно прав для добавления записей в данный раздел', './');
        }
        if ($isEdit && !$this->canEdit) {
            AdminPlugAlert::error('Ошибка! Недостаточно прав для редактирования записей в данном разделе', './');
        }

        $row = & $this->row;
        $row = array();
        foreach ($this->fields as $index => $field) {
            if ($field instanceof SFFVirtual) {
                unset($this->fields[$index]);
            }
        }
        foreach ($this->fields as $field) {
            $row[$field->name] = $field->defval();
            $field->loadUI(true);
        }

        foreach ($this->fks as $field) {
            $fv = & $_SESSION[$this->table]['filter'][$field->name];
            if (@$fv !== '' && @$fv !== NULL) {
                $row[$field->name] = (int) @$fv;
            }
        }

        if (!empty($_POST)) {
            $row = $_POST + $row;
        }

        $pk = 0;
        if (!empty($_REQUEST[$this->pk->name])) {
            $pk = (int) $_REQUEST[$this->pk->name];
            $where[] = $this->pk->name . "=" . $pk;
            $q = "SELECT * FROM " . $this->table . " WHERE " . join(' AND ', $where);
            if ($rrow = SFDB::result($q)) {
                $row = $rrow;
                $title = 'Редактировать запись №' . $pk;
                $rowName = '';
                if (isset($row['title'])) {
                    $rowName = $row['title'];
                } elseif (isset($row['name'])) {
                    $rowName = $row['name'];
                } elseif (isset($row['label'])) {
                    $rowName = $row['label'];
                }
                if ($rowName) {
                    mb_strlen($rowName, 'utf8') > 50 ? $rowName = mb_substr($rowName, 0, 47, 'utf8') . '...' : null;
                    $title .= ' &mdash; &laquo;' . $rowName . '&raquo;';
                }
            } else {
                AdminPlugAlert::error('Ошибка! Запись не найдена или недостаточно прав для редактирования.', './');
            }
        }

        if ($isGroup) {
            $title = 'Редактирование нескольких записей';
            $ids = SFDB::escape($_GET['ids']);
            $q = "SELECT * FROM $this->table WHERE {$this->pk->name} in (" . $ids . ")";
            $rows = SFDB::assoc($q);
            foreach ($this->fields as $field) {
                $vars = array();
                foreach ($rows as $row0) {
                    $vars[$row0[$field->name]] = true;
                }
                if (count($vars) == 1) {
                    foreach ($vars as $var => $useless) {
                        $row[$field->name] = $var;
                    }
                }
            }

            $hasSetParams = false;
            foreach ($this->fields as $field) {
                if ($field->name == 'params') {
                    $hasSetParams = true;
                    break;
                }
            }

            if ($hasSetParams) {
                $params = array();
                foreach ($this->params as $paramz) {
                    foreach ($paramz as $group) {
                        if (isset($group['fields'])) {
                            foreach ($group['fields'] as $field) {
                                if (is_object($field)) {
                                    if ($group['name']) {
                                        $vars = array();
                                        foreach ($rows as $row0) {
                                            $rowParams = unserialize($row0['params']);
                                            @$vars[$rowParams[$group['name']][$field->name]] = true;
                                        }
                                        if (count($vars) == 1) {
                                            foreach ($vars as $var => $useless) {
                                                $params[$group['name']][$field->name] = $var;
                                            }
                                        }
                                    } else {
                                        $vars = array();
                                        foreach ($rows as $row0) {
                                            $rowParams = unserialize($row0['params']);
                                            $vars[$rowParams[$field->name]] = true;
                                        }
                                        if (count($vars) == 1) {
                                            foreach ($vars as $var => $useless) {
                                                $params[$field->name] = $var;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (isset($group['field'])) {
                            $vars = array();
                            foreach ($rows as $row0) {
                                $rowParams = unserialize($row0['params']);
                                $vars[$rowParams[$field->name]] = true;
                            }
                            if (count($vars) == 1) {
                                foreach ($vars as $var => $useless) {
                                    $params[$field->name] = $var;
                                }
                            }
                        }
                    }
                }
                $row['params'] = serialize($params);
            }
        }

        if (SFAdminCore::$isAjax) {
            if (!empty($_REQUEST['field']) && isset($this->fields[$_REQUEST['field']]) && $pk) {
                $field = $this->fields[$_REQUEST['field']];
                $q = "UPDATE " . $this->table . " SET " . $field->name . "='' WHERE " . join(' AND ', $where);
                SFDB::query($q);
                $field->delete($row[$field->name]);
                exit();
            }
        }

        $params = array();
        if (!empty($row['params'])) {
            $params = unserialize($row['params']);
        }

        include dirname(__FILE__) . '/tpl/form.tpl';
    }

    /**
     * Вывод html-кода для ввода данных на форме
     * @param SFField $field
     * @param array $row
     * @return string
     */
    protected function formFieldInput($field, $row)
    {
        return $field->input(@$row[$field->name]);
    }

    /**
     * return @void
     */
    public function showActions() {
        include 'tpl/show.actions.tpl';
    }

    public function rowActions($row) {
        include 'tpl/show.actions.row.tpl';
    }

    public function copy() {
        $q = "SELECT * FROM $this->table WHERE {$this->pk->name} in ($this->ids)";
        $rows = SFDB::assoc($q);
        $fieldNames = array('name', 'title', 'label');
        foreach ($rows as $row) {
            $set = array();
            foreach ($row as $key => $value) {
                if ($key == $this->pk->name) {
                    continue;
                }
                if (in_array($key, $fieldNames)) {
                    $value = "Копия $value";
                }
                if ($value === null) {
                    
                } else {
                    $set[] = "$key = '$value'";
                }
            }
            $q = "INSERT INTO $this->table SET " . implode(', ', $set);
            $success = SFDB::query($q);
            if ($success) {
                $insertID = SFDB::insertID();
                AdminPlugAlert::success("Запись №{$row[$this->pk->name]} скопирована в №$insertID");
            } else {
                AdminPlugAlert::error("Запись №{$row[$this->pk->name]} не скопирована!");
                AdminPlugAlert::error($q);
                AdminPlugAlert::error(SFDB::error());
            }
        }
        header("location: ./");
        exit;
    }

    public function validate() {
        foreach ($this->fields as $field) {
            if ($errors = $field->check()) {
                foreach ($errors as $error) {
                    $this->errors[$field->name][] = $error;
                }
            }
        }
        if (SFAdminCore::$isAjax) {
            $json = array();
            $json['valid'] = count($this->errors) ? 0 : 1;
            if (count($this->errors)) {
                foreach ($this->errors as $field => $errors) {
                    $json['errors'][$field] = join('<br />', $errors);
                }
            }
            echo json_encode($json);
        } else {
            return count($errors) ? false : true;
        }
    }

    public function saveGroup() {
        $success = true;
        $ids = @$_POST['group_ids'];
        $set = array();
        foreach ($this->fields as $index => $field) {
            if (isset($_POST['set'][$field->name]) && !($field instanceof SFFVirtual)) {
                $value = $field->getPOST();
                $set[] = "$field->name = '$value'";
            }
        }

        $hasSetParams = false;
        foreach ($this->fields as $field) {
            if ($field->name == 'params' && isset($_POST['set_param'])) {
                $hasSetParams = true;
                break;
            }
        }

        if ($hasSetParams) {
            $q = "SELECT {$this->pk->name} id, params FROM $this->table WHERE {$this->pk->name} in ($ids)";
            $rows = SFDB::assoc($q);
            foreach ($rows as $row) {
                $params = unserialize($row['params']);
                if (!is_array($params)) {
                    $params = array();
                }

                $MY_POST = $_POST;
                foreach ($this->params as $paramz) {
                    foreach ($paramz as $group) {
                        if (isset($group['fields'])) {
                            foreach ($group['fields'] as $field) {
                                if (isset($_POST['set_param'][$field->name]) && is_object($field)) {
                                    if ($group['name']) {
                                        $_POST = $MY_POST[$group['name']];
                                        $value = $field->getPost(true);
                                        $value = preg_replace("@^'(.+)'$@", '$1', $value);
                                        $params[$group['name']][$field->name] = $value;
                                    } else {
                                        $_POST = $MY_POST;
                                        $value = $field->getPost(true);
                                        $value = preg_replace("@^'(.+)'$@", '\1', $value);
                                        $params[$field->name] = $value;
                                    }
                                }
                            }
                        }
                        if (isset($group['field'])) {
                            $field = $group['field'];
                            $_POST = $MY_POST;
                            $value = $field->getPost(true);
                            $value = preg_replace("@^'(.+)'$@", '\1', $value);
                            $params[$field->name] = $value;
                        }
                    }
                }
                $_POST = $MY_POST;

                $q = "UPDATE $this->table SET params = '" . serialize($params) . "' WHERE {$this->pk->name} = {$row['id']}";
                $success &= SFDB::query($q);
            }
        }

        if (count($set)) {
            $q = "UPDATE $this->table SET " . implode(', ', $set) . " WHERE {$this->pk->name} in ($ids)";
            $success &= SFDB::query($q);
        }

        if ($success) {
            AdminPlugAlert::success('Записи ' . $ids . ' успешно сохранены!');
        } else {
            AdminPlugAlert::error('Записи ' . $ids . ' не сохранены!');
        }
        return 'ids';
//        header("location: ./");
//        exit;
    }

    /**
     * 
     * @return bool|int pkValue | false
     */
    public function save() {

        if (!empty($_POST['group_ids'])) {
            return $this->saveGroup();
        }

        if ($this->validate()) {

            foreach ($this->fields as $index => $field) {
                if ($field instanceof SFFVirtual) {
                    unset($this->fields[$index]);
                }
            }

            $isInsert = empty($_POST[$this->pk->name]);
            $isUpdate = !$isInsert;
            $q = $isInsert ? $this->getQueryInsert() : $this->getQueryUpdate();
//            echo $q;die;

            SFDB::query($q);
            if (SFDB::errno()) {
                $this->errors[] = "Введенные данные некорректны";
                $this->errors[] = SFDB::error();
            } else {
                if (empty($_POST[$this->pk->name])) {
                    $_POST[$this->pk->name] = SFDB::insertID();
                }
                $pkValue = $_POST[$this->pk->name];

                $field_alias = false;
                $field_path = false;
                foreach ($this->fields as $field) {
                    if ($field instanceof SFFAlias) {
                        $field_alias = $field;
                    }
                    if ($field instanceof SFFPath) {
                        $field_path = $field;
                    }
                }
                if ($field_path && $field_alias) {
                    if ($this->pid) {
                        $where = $this->where_sys;
                        $where[] = "" . $this->table . "." . $this->pk->name . "=" . (int) $_POST[$this->pk->name];
                        $q = "
                            SELECT " . $this->table . "." . $this->pk->name . ", " . $this->table . "." . $this->pid->name . ", " . $this->table . "." . $field_alias->name . ", t2." . $field_path->name . "
                            FROM " . $this->table . "
                            LEFT JOIN " . $this->table . " t2 ON t2." . $this->pk->name . " = " . $this->table . "." . $this->pid->name . "
                            WHERE " . join(" AND ", $where) . "
                        ";
                        $row = SFDB::result($q);
                        $path = $row['path'] ? $row['path'] . $row['alias'] . '/' : '/' . $row['alias'] . '/';
                        $path = preg_replace('@\/+@', '/', $path);
                        $row[$field_path->name] = $path;

                        $q = "UPDATE " . $this->table . " SET " . $field_path->name . "='" . $path . "' WHERE " . join(" AND ", $where);
                        SFDB::query($q);

                        $steck[] = $row;
                        $i = 0;
                        $counter = 0;
                        while ($i < count($steck)) {
                            $cur = $steck[$i];
                            $where = $this->where_sys;
                            $where[] = "" . $this->table . "." . $this->pid->name . "=" . $cur[$this->pk->name];
                            $q = "
                                SELECT " . $this->pk->name . ", " . $field_alias->name . ", " . $field_path->name . "
                                FROM " . $this->table . "
                                WHERE " . join(" AND ", $where) . "
                            ";
                            $rows = SFDB::assoc($q);
                            foreach ($rows as $row) {
                                if ($row[$field_path->name] != $cur[$field_path->name] . $row[$field_alias->name] . '/') {
                                    $counter++;
                                    $path = $cur[$field_path->name] . $row[$field_alias->name] . '/';
                                    $where = $this->where_sys;
                                    $where[] = "" . $this->table . "." . $this->pk->name . "=" . $row[$this->pk->name];
                                    $q = "UPDATE " . $this->table . " SET " . $field_path->name . "='" . $path . "' WHERE " . join(" AND ", $where);
                                    SFDB::query($q);
                                    $row[$field_path->name] = $path;

                                    $steck[] = $row;
                                }
                            }
                            $i++;
                        }
                    } else {
                        $where = $this->where_sys;
                        $where[] = "" . $this->table . "." . $this->pk->name . "=" . (int) $_POST[$this->pk->name];
                        $path = '/' . $_POST[$field_alias->name] . '/';
                        $q = "UPDATE " . $this->table . " SET " . $field_path->name . "='" . $path . "' WHERE " . join(" AND ", $where);
                        SFDB::query($q);
                    }
                }
            }
            if (count($this->errors)) {
                AdminPlugAlert::error('Ошибка при сохранении записи' . ($pkValue ? " №$pkValue" : ''));
                $this->form();
            } else {
                AdminPlugAlert::success('Запись №' . $pkValue . ' успешно сохранена!');
            }
        }
        return count($this->errors) ? false : $pkValue;
    }

    protected function getQueryInsert() {
        $keys = $values = array();
        foreach ($this->fields as $field) {
            if ($field->isVirtual) {
                $field->getPOST();
            }
            $atq = !$field->isVirtual && !$field->readonly || $field instanceof SFFPath;
            $post = $field->getPOST();
            if ($atq) {
                if (empty($post) || $post == 'NULL' || $post == "''") {
                    $cinfo = SFDB::columnInfo($this->table, $field->name);
                    $atq = empty($cinfo['Default']);
                }
            }
            if ($atq) {
                $keys[] = $field->name;
                if ($field->name == 'params') {
                    $params = $this->getParams();
                    $post = "'" . SFDB::escape(serialize($params)) . "'";
                }
                $values[] = $post;
            }
        }
        $q = "INSERT INTO " . $this->table . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ")";
        return $q;
    }

    protected function getQueryUpdate() {
        $q = "SELECT * FROM " . $this->table . " WHERE " . $this->pk->name . "=" . (int) $_POST[$this->pk->name];
        $row = SFDB::result($q);

        $q = "UPDATE " . $this->table . " SET";
        foreach ($this->fields as $field) {
            if ($field->isVirtual) {
                $field->getPOST();
            } elseif (!$field->readonly) {
                if ($field->name == 'params') {
                    $params = $this->getParams();
                    $q .= " " . $field->name . "='" . SFDB::escape(serialize($params)) . "',";
                } elseif ($field instanceof SFFPassword) {
                    $value = $field->getPOST();
                    if ($value) {
                        $q .= " " . $field->name . "=" . $value . ",";
                    }
                } elseif ($field instanceof SFFDateTime) {
                    $cinfo = SFDB::columnInfo($this->table, $field->name);
                    // && 'on update CURRENT_TIMESTAMP' == $cinfo['Extra']
                    if (empty($cinfo['Extra'])) {
                        $value = $field->getPOST();
                        if ($value) {
                            $q .= " " . $field->name . "=" . $value . ",";
                        }
                    }
                } elseif ($field instanceof SFFFile) {
                    if ($value = trim($field->getPOST(), "'")) {
                        $q .= " `" . $field->name . "`='" . $value . "',";
                    }
                } else {
                    $value = $field->getPOST();
                    $q .= " " . $field->name . "=" . $value . ",";
                }
            }
        }
        $q = substr($q, 0, -1);
        $q .= " WHERE " . $this->pk->name . "=" . (int) $_POST[$this->pk->name];
        /*
          echo $q;
          die();
         * 
         */
        return $q;
    }

    protected function getParams() {
        $params = array();
        $MY_POST = $_POST;
        foreach ($this->params as $paramz) {
            foreach ($paramz as $group) {
                if (isset($group['fields'])) {
                    foreach ($group['fields'] as $field) {
                        if (is_object($field)) {
                            if ($group['name']) {
                                $_POST = $MY_POST[$group['name']];
                                $value = $field->getPost(true);
                                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                                $params[$group['name']][$field->name] = $value;
                            } else {
                                $_POST = $MY_POST;
                                $value = $field->getPost(true);
                                $value = preg_replace("@^'(.+)'$@", '\1', $value);
                                $params[$field->name] = $value;
                            }
                        }
                    }
                }
                if (isset($group['field'])) {
                    $field = $group['field'];
                    $_POST = $MY_POST;
                    $value = $field->getPost(true);
                    $value = preg_replace("@^'(.+)'$@", '\1', $value);
                    $params[$field->name] = $value;
                }
            }
        }
        $_POST = $MY_POST;
        return $params;
    }

    /**
     * Удаляет одну запись
     * @param int $id
     * @return int errno
     */
    protected function deleteItem($id) {
        $where = $this->where_sys;
        $where[] = $this->pk->name . "=" . (int) $id;
        $q = "DELETE FROM " . $this->table . " WHERE " . join(' AND ', $where);
        SFDB::query($q);
        return SFDB::errno() ? SFDB::errno() . ': ' . SFDB::error() : '';
    }

    /**
     * Обработчик кнопки "удалить"
     * @return void
     */
    public function delete() {

        if (!$this->canDelete) {
            AdminPlugAlert::error('Ошибка! Недостаточно прав для удаления записей в данном разделе', './');
        }

        $ids_temp = (isset($_REQUEST['rows']) && is_array($_REQUEST['rows'])) ? $_REQUEST['rows'] : array();
        $ids = array();
        foreach ($ids_temp as $id) {
            if ((int) $id > 0) {
                $ids[] = (int) $id;
            }
        }

        foreach ($this->fields as $field) {
            if ($field instanceof SFFFile) {
                $dir = 'files';
                if ($field instanceof SFFImage) {
                    $dir = 'images';
                }
                $q = "SELECT " . $field->name . " FROM " . $this->table . " WHERE " . $this->pk->name . " IN (" . join(',', $ids) . ")";
                $rows = SFDB::assoc($q);
                foreach ($rows as $row) {
                    if ($row[$field->name] && is_file('../uf/' . $dir . '/' . $field->path . $row[$field->name])) {
                        unlink('../uf/' . $dir . '/' . $field->path . $row[$field->name]);
                        if (isset($field->sizes)) {
                            foreach ($field->sizes as $size) {
                                if (is_file('../uf/' . $dir . '/' . $field->path . $size . '/' . $row[$field->name])) {
                                    unlink('../uf/' . $dir . '/' . $field->path . $size . '/' . $row[$field->name]);
                                }
                            }
                        }
                    }
                }
            }
        }

        $deleted = array();
        foreach ($ids as $id) {
            $n = $this->deleteItem($id);
            if ($n) {
                AdminPlugAlert::error("#$id &mdash; Ошибка. Удаление записи не возможно");
                AdminPlugAlert::error("#$id &mdash; $n");
            } else {
                $deleted[] = $id;
                AdminPlugAlert::success("#$id &mdash; Успех. Запись удалена");
            }
        }
        header("location: ./");
        exit;
    }

    protected function portlets($position = 'right') {
        
    }
    
    /**
     * 
     * @param int $id
     * @return array
     */
    protected function getRow($id) {
        $id = (int) $id;
        $q = "SELECT * FROM $this->table WHERE {$this->pk->name} = $id";
        $row = SFDB::result($q);
        return $row;
    }

    /**
     * 
     * @param SFDBWhere $where
     * @param bool [optional = true] $withKeys - Если true, ключи массива будут ID записей
     * @return array
     */
    protected function getRows(SFDBWhere $where = null, $withKeys = true) {
        $q = "SELECT * FROM $this->table $where";
        $rows = SFDB::assoc($q, $withKeys ? $this->pk->name : false);
        return $rows;
    }


}
