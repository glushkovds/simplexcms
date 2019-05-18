<?php

class AdminModUser extends SFModBase {

    public function content() {
        SFAdminPage::js('/admin/theme/js/user.js',15);
        $uinfo = SFUser::info();
        unset($uinfo['hash']);
        unset($uinfo['hash_admin']);
        unset($uinfo['password']);
        echo '<input type="hidden" id="sfuser-info" value="' . str_replace('"', "'", json_encode($uinfo)) . '" />' . "\n";
        echo '<input type="hidden" id="sfuser-privs" value="' . str_replace('"', "'", json_encode(SFUser::ican())) . '" />' . "\n";
    }

}
