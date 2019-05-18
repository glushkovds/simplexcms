<?php

class SitemapContent extends SFSitemapBase {

    public function rows() {
        $ret = [];
        $q = "SELECT path FROM content WHERE active = 1";
        $rows = SFDB::assoc($q);
        foreach ($rows as $row) {
            $ret[$row['path']] = [];
        }
        return $ret;
    }

}
