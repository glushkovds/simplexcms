<?php

class AdminModFile extends SFModBase {

    private $filesDir;

    public function __construct($module) {
        parent::__construct($module);
        $this->filesDir = $_SERVER['DOCUMENT_ROOT'] . '/uf/files';
    }

    public function content() {
        if (isset($_REQUEST['sf_module_id']) && $action = @$_GET['action']) {
            if (method_exists($this, $action)) {
                $this->$action();
            } else {
                $this->error404();
            }
            exit;
        }

        SFAdminPage::js('/admin/mod/file/js/file.js');
//        echo 123;die;
//        include dirname(__FILE__) . '/tpl/menu.tpl';
    }

    public function index() {
        $relName = (string) @$_REQUEST['rel_name'];
        $relId = (int) @$_REQUEST['rel_id'];
        if (empty($relName) || empty($relId)) {
            echo 'Неверные параметры!';
            return;
        }
        $q = "SELECT * FROM file WHERE rel_name = '" . SFDB::escape($relName) . "' AND rel_id = $relId";
        $rows = SFDB::assoc($q);
        foreach ($rows as $index => $row) {
            $rows[$index] = $this->patchFileRow($row);
        }
        $maxFileSize = ((int) $umax = ini_get('upload_max_filesize')) * pow(1024, ['K' => 1, 'M' => 2, 'G' => 3][substr($umax, -1)]);
        $maxFileSizeUser = str_replace(['K', 'M', 'G'], ['Кб', 'Мб', 'Гб'], ini_get('upload_max_filesize'));
        ob_start();
        include 'tpl/index.tpl';
        $modal = ['title' => 'Файлы', 'body' => ob_get_clean()];
        include 'tpl/modal.tpl';
    }

    public function upload() {
        $relName = (string) @$_REQUEST['rel_name'];
        $relId = (int) @$_REQUEST['rel_id'];
        if (empty($relName) || empty($relId)) {
            echo json_encode(['success' => false, 'error' => 'Неверные параметры!']);
            return;
        }
        $postName = 'file';
        $fileInfo = (array) @$_FILES[$postName];
        if (!$fileInfo || !is_uploaded_file($fileInfo['tmp_name'])) {
            echo json_encode(['success' => false, 'error' => self::getUploadError(@$fileInfo['error'])]);
            return;
        }

        $fsName = microtime(true) . '.' . PlugFS::fileExtension($fileInfo['name']);
        if (!@move_uploaded_file($fileInfo['tmp_name'], "$this->filesDir/$fsName")) {
            echo json_encode(['success' => false, 'error' => 'Не удалось сохранить загруженный файл']);
            return;
        }

        $q = "
            INSERT INTO file SET name = '" . SFDB::escape($fileInfo['name']) . "', fsname = '" . SFDB::escape($fsName) . "',
                rel_name = '" . SFDB::escape($relName) . "', rel_id = $relId
        ";
        $success = SFDB::query($q);
        if (!$success) {
            unlink("$this->filesDir/$fsName");
            echo json_encode(['success' => false, 'error' => 'Не удалось записать загруженный файл']);
            return;
        }

        ob_start();
        $q = "SELECT * FROM file WHERE file_id = " . (int) SFDB::insertID();
        $row = $this->patchFileRow((array) @SFDB::result($q));
        include 'tpl/index.item.tpl';
        echo json_encode(['success' => true, 'item_html' => ob_get_clean()]);
        return;
    }

    public function download() {
        $fileId = (int) @$_REQUEST['file_id'];
        $q = "SELECT * FROM file WHERE file_id = $fileId";
        $row = SFDB::result($q);
        if (!$row) {
            AdminPlugAlert::error('Файл не найден', './');
        }
        PlugDownload::sendFile("$this->filesDir/{$row['fsname']}", $row['name']);
    }

    public function delete() {
        $fileId = (int) @$_REQUEST['file_id'];
        $q = "SELECT * FROM file WHERE file_id = $fileId";
        $row = SFDB::result($q);
        $error = '';
        if (!$row) {
            $error = 'Файл не найден';
        }
        if (!$error && !unlink("$this->filesDir/{$row['fsname']}")) {
            $error = 'Ошибка удаления файла! Обратитесь к системному администратору.';
        }
        if (!$error) {
            $q = "DELETE FROM file WHERE file_id = $fileId";
            $success = SFDB::query($q);
            if (!$success) {
                $error = 'Ошибка удаления файла в БД! Обратитесь к разработчику.';
            }
        }

        $ret = $error ? ['success' => false, 'error' => $error] : ['success' => true];
        echo json_encode($ret);
    }

    private function patchFileRow($row) {
        $row['size'] = filesize("$this->filesDir/{$row['fsname']}");
        $row['size_user'] = PlugFS::fileSizeStr($row['size']);
        $row['ext'] = PlugFS::fileExtension($row['name']);
        $row['url'] = "?sf_module_id=$this->id&action=download&file_id={$row['file_id']}";
        return $row;
    }

    public function error404() {
        echo 'Получен некорректный запрос!';
    }

    protected static function getUploadError($code) {
        $lib = [
            UPLOAD_ERR_INI_SIZE => 'Размер принятого файла превысил максимально допустимый размер',
            UPLOAD_ERR_FORM_SIZE => 'Размер загружаемого файла превысил максимально допустимый размер',
            UPLOAD_ERR_PARTIAL => 'Загружаемый файл был получен только частично.',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен.',
            UPLOAD_ERR_NO_TMP_DIR => ' Отсутствует временная папка.',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
            UPLOAD_ERR_EXTENSION => 'PHP-расширение остановило загрузку файла.'
        ];
        return @$lib[$code] ?: 'Неизвестная ошибка.';
    }

}
