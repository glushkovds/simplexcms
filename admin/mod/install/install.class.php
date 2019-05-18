<?php

class AdminModInstall extends SFModBase {

    private $installDir = '';

    protected function content() {
        AdminPlugAlert::output();
        if (!empty($_GET['installdir'])) {
            $this->installDir = urldecode($_GET['installdir']);
        }
        if (is_uploaded_file(@$_FILES['file']['tmp_name'])) {
            $this->installDir = $this->fromZip($_FILES['file']);
        }
        if ($this->installDir) {
            return $this->install();
        }
        include 'tpl/load.form.tpl';
    }

    protected function install() {
        $installer = $this->getInstaller();
        if ($installer) {
            if ($installer->destDir) {
                if (isset($_GET['confirm'])) {
                    $success = $installer->install();
                    if ($success) {
                        $text = $installer->type == SFAdminInstallBase::TYPE_EXT ? "Расширение $installer->destDir успешно установлено" : "Плагин $installer->destDir успешно установлен";
                        PlugFS::rmDir($this->installDir);
                        AdminPlugAlert::success($text, './');
                    } else {
                        AdminPlugAlert::error('Ошибка! Установка не удалась в процессе установки', './');
                    }
                }
                include 'tpl/confirm.tpl';
                return;
            } else {
                AdminPlugAlert::error('Ошибка! В инсталляторе не указан адрес, куда устанавливать ($destDir)');
            }
        } else {
            AdminPlugAlert::error('Ошибка! Не удалось найти инсталлятор');
        }
        PlugFS::rmDir($this->installDir);
        header("location: ./");
        exit;
    }

    /**
     * 
     * @return \SFAdminInstallBase|boolean
     */
    protected function getInstaller() {
        $file = 'sfinstall.php';
        $path = '';
        if (is_file($ret = "$this->installDir/$file")) {
            $path = $ret;
        } else {
            foreach (scandir($this->installDir) as $dir) {
                if (is_dir($dir0 = "$this->installDir/$dir")) {
                    if (is_file($ret = "$dir0/$file")) {
                        $path = $ret;
                        break;
                    } else {
                        foreach (scandir($dir0) as $dir1) {
                            if (is_dir($dir0 = "$this->installDir/$dir/$dir1")) {
                                if (is_file($ret = "$dir0/$file")) {
                                    $path = $ret;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($path) {
            include $path;
            return new SFInstall(dirname($path));
        }

        return false;
    }

    protected function getTmpDir() {
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/uf/install';
        $success = true;
        if (!is_dir($dir)) {
            $success = mkdir($dir);
        }
        if (!$success) {
            AdminPlugAlert::error('Ошибка! Невозможно создать root-папку ' . $dir, './');
        }
        return $success ? $dir : false;
    }

    protected function fromZip($uploadedFile) {
        $root = $this->getTmpDir();
        $dir = $root . '/' . time();
        if (!mkdir($dir)) {
            AdminPlugAlert::error('Ошибка! Невозможно создать папку установщика ' . $dir, './');
        }
        $zipFile = $dir . '/' . $uploadedFile['name'];
        if (move_uploaded_file($uploadedFile['tmp_name'], $zipFile)) {
            if (PlugZip::unzip($zipFile)) {
                return $dir;
            } else {
                AdminPlugAlert::error('Ошибка! Невозможно распаковать архив ' . $zipFile, './');
            }
        } else {
            AdminPlugAlert::error('Ошибка! Невозможно записать архив установщика в папку ' . $dir, './');
        }
    }

}
