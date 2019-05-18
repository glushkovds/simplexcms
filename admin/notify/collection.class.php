<?php

class SFAdminNotifyCollection {

    private static $instance;

    /**
     * 
     * @return AdminNotifyCollection
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    private $items = array();

    private function __construct() {
        
    }

    public function content() {
        $this->collect();
        $cnt = count($this->items);
        include 'tpl/index.tpl';
    }

    private function collect() {
        $extRoot = $_SERVER['DOCUMENT_ROOT'] . '/ext';
        $exts = array_slice(scandir($extRoot), 2);
        foreach ($exts as $ext) {
            $class = 'AdminNotify' . ucfirst($ext);
            if (class_exists($class)) {
                $items = $class::get();
                if (is_array($items) && count($items)) {
                    $this->items = array_merge($this->items, $items);
                }
            }
        }
    }

}
