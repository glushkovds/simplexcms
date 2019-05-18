<?php

class AdminStructTable extends AdminBase {
    
    public function __construct() {
        parent::__construct();
        SFAdminPage::js('/admin/base/js/struct.table.js');
    }

    protected function portlets($position = 'right') {
        parent::portlets($position);
        if ('right' == $position) {
            if (isset($this->row['table_id'])) {

                if ($this->ids) {
                    
                } else {

                    $q = "SELECT * FROM user_role order by role_id";
                    $roles = SFDB::assoc($q);

                    $q = "SELECT * FROM struct_table_right WHERE table_id = {$this->row['table_id']}";
                    $rights = SFDB::assoc($q, 'role_id');

                    include 'tpl/struct_table/rights.tpl';
                }
            }
        }
    }

    public function save() {
        if ($this->ids) {
            return $this->saveGroup();
        }

        $ret = parent::save();
        $pkValue = $_POST[$this->pk->name];

        $q = "DELETE FROM struct_table_right WHERE table_id = $pkValue";
        SFDB::query($q);

        if ($rights = @$_POST['rights']) {
            foreach ($rights as $roleId => $roleRights) {
                $q = "
                    INSERT INTO struct_table_right SET 
                    table_id = $pkValue, role_id = $roleId,
                    can_add = " . (int) isset($roleRights['can_add']) . ", 
                    can_edit = " . (int) isset($roleRights['can_edit']) . ", 
                    can_delete = " . (int) isset($roleRights['can_delete']) . "
                ";
                SFDB::query($q);
            }
        }

        return $ret;
    }

}
