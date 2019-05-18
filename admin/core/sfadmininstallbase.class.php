<?php

abstract class SFAdminInstallBase {

    const TYPE_EXT = 0;
    const TYPE_PLUG = 1;

    public $type = self::TYPE_EXT;
    public $hasSetup = false;
    public $destDir;
    protected $installDir;

    public function __construct($istallDir) {
        $this->installDir = $istallDir;
    }

    public function install() {
        $success = $this->copyFiles() && $this->initDB();
        return $success;
    }

    protected function copyFiles() {
        $type = $this->type == self::TYPE_EXT ? 'ext' : 'plug';
        $dest = "{$_SERVER['DOCUMENT_ROOT']}/$type/$this->destDir";
        if (is_dir($dest)) {
            AdminPlugAlert::error("Папка $dest уже существует");
        }
        $success = PlugFS::moveDir($this->installDir . '/data', $dest);
        return $success;
    }

    protected function initDB() {
        return true;
    }

    protected static function queryFromFile($file, $replaces = array(), $debug = false) {
        $success = true;
        $qr = file_get_contents($file);
        if (count($replaces)) {
            $qr = str_replace(array_keys($replaces), array_values($replaces), $qr);
        }
        $qs = array_filter(array_map('trim', explode('---', $qr)));

        foreach ($qs as $q) {
            $success &= SFDB::query($q);
            if (!$success && $debug) {
                echo $q;
                die;
            }
        }
        return $success;
    }

}
