<?php

class AdminPlugLog {

    public static function a($action, $data) {
//        $q = "DELETE FROM log WHERE ADDDATE(datetime,INTERVAL 6 MONTH) < NOW()";
//        SFDB::query($q);
        $action = SFDB::escape($action);
        $data = SFDB::escape($data);
        $browser = SFDB::escape($_SERVER['HTTP_USER_AGENT']);
        $set = array("action = '$action', ip = '{$_SERVER['REMOTE_ADDR']}', browser = '$browser'");
        $set[] = "data = '$data'";
        $q = "INSERT INTO log SET " . implode(', ', $set);
        SFDB::query($q);
    }

}
