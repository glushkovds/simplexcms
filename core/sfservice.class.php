<?php

class SFService {

    private static $list = array();

    private function __construct() {
        
    }

    public static function &tree2list(&$tree, $id_start = 0, $p_only = false) {
        self::$list = array();
        if (isset($tree[$id_start])) {
            self::tree2listBuild($tree, $id_start, $p_only);
        }
        return self::$list;
    }

    private static function tree2listBuild(&$tree, $pid, $p_only, $level = 0) {
        if ($p_only) {
            foreach ($tree[$pid] as $id => $item) {
                if (isset($tree[$id])) {
                    $item['tree_level'] = $level;
                    $item['tree_nchild'] = isset($tree[$id]) ? count($tree[$id]) : 0;
                    self::$list[$id] = $item;
                    self::tree2listBuild($tree, $id, $p_only, $level + 1);
                }
            }
        } else {
            foreach ($tree[$pid] as $id => $item) {
                $item['tree_level'] = $level;
                $item['tree_nchild'] = isset($tree[$id]) ? count($tree[$id]) : 0;
                self::$list[$id] = $item;
                if (isset($tree[$id])) {
                    self::tree2listBuild($tree, $id, $p_only, $level + 1);
                }
            }
        }
    }

}
