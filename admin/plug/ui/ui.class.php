<?php

class AdminPlugUI {
    
    public static function datePicker() {
        SFAdminPage::js('/admin/theme/ui/datepicker/bootstrap-datepicker.js', 5);
        SFAdminPage::js('/admin/theme/ui/datepicker/bootstrap-datepicker.ru.js', 5);
        SFAdminPage::css('/admin/theme/ui/datepicker/datepicker.css', 5);
    }
    
    public static function timePicker() {
        SFAdminPage::js('/admin/theme/ui/timepicker/bootstrap-timepicker.min.js', 5);
        SFAdminPage::css('/admin/theme/ui/timepicker/bootstrap-timepicker.min.css', 5);
    }
    
    public static function dateTimePicker() {
        SFAdminPage::js('/admin/theme/ui/datetimepicker/bootstrap-datetimepicker.js', 5);
        SFAdminPage::js('/admin/theme/ui/datetimepicker/bootstrap-datetimepicker.ru.js', 5);
        SFAdminPage::css('/admin/theme/ui/datetimepicker/datetimepicker.css', 5);
    }
    
    public static function fileInput() {
        SFAdminPage::js('/admin/theme/ui/fileinput/bootstrap-fileinput.js', 5);
        SFAdminPage::css('/admin/theme/ui/fileinput/bootstrap-fileinput.css', 5);
    }
    
    public static function treeView() {
        SFAdminPage::js('/admin/theme/ui/treeview/jstree.min.js', 5);
        SFAdminPage::js('/admin/theme/ui/treeview/ui-tree.js', 5);
        SFAdminPage::css('/admin/theme/ui/treeview/style.min.css', 5);
    }
    
}