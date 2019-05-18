<?php

class PlugFS {

    /**
     * Рекурсивное удаление папки
     * @param string $dir
     */
    public static function rmDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        self::rmDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    /**
     * Рекурсивное удаление содержимого папки без удаления самой папки
     * @param string $dir
     */
    public static function clearDir($dir, $level = 0) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        self::clearDir($dir . "/" . $object, ++$level);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            if ($level > 0) {
                rmdir($dir);
            }
        }
    }

    /**
     * Возвращает название файла БЕЗ расширения
     * @param string $filename Название файла
     * @return string
     */
    public static function fileName($filename) {
        $info = pathinfo($filename);
        return $info['filename'];
    }

    /**
     * Возвращает расширение файла
     * @param string $filename Название файла
     * @return string
     */
    public static function fileExtension($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * Возвращает читаемый размер файла (например: 6.12 Mb)
     * @param string $filename Название файла
     * @param int $round - округлять до
     * @return string
     */
    public static function fileSize($filepath, $round = 2) {
        $size = @(int) filesize($filepath);
        $unit = 'b';
        if ($size > 1000) {
            $size = round($size / 1024, $round);
            $unit = 'Kb';
        }
        if ($size > 1000) {
            $size = round($size / 1024, $round);
            $unit = 'Mb';
        }
        if ($size > 1000) {
            $size = round($size / 1024, $round);
            $unit = 'Gb';
        }
        return $size . ' ' . $unit;
    }

    /**
     * 
     * @param type $src
     * @param type $dst
     */
    public static function copyDir($src, $dst) {
        $success = true;
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $success &= self::copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $success &= copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return $success;
    }

    /**
     * 
     * @param type $src
     * @param type $dst
     * @return bool
     */
    public static function moveDir($src, $dst) {
        return rename($src, $dst);
    }

    /**
     * Загрузка пользовательского файла 
     * @param array $file элемент массива $_FILES
     * @param string $destDir [optional = ''] куда складываем. Путь относительно /uf/files.
     *                        Например $destDir = 'docs', файл будет сохранен в /uf/files/docs, $destDir = '', файл будет сохранен в /uf/files
     * @param string $fileName [optional = null] Имя сохраняемого файла без расширения (например бер .txt). Если не указать, сгенерируется
     * @return array success, error, error_text, filename, fullpath, relpath, url
     */
    public static function upload($file, $destDir = '', $fileName = null) {


        $errorTexts = [
            UPLOAD_ERR_OK => 'Файл загружен без ошибок',
            UPLOAD_ERR_INI_SIZE => 'Размер принятого файла превысил максимально допустимый размер',
            UPLOAD_ERR_FORM_SIZE => 'Размер принятого файла превысил максимально допустимый размер формы',
            UPLOAD_ERR_PARTIAL => 'Загружаемый файл был получен только частично',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Ошибка на сервере', // Отсутствует временная папка.
            UPLOAD_ERR_CANT_WRITE => 'Внутренняя ошибка на сервере', // Не удалось записать файл на диск
            UPLOAD_ERR_EXTENSION => 'Внутренние ошибки на сервере', // PHP-расширение остановило загрузку файла. // Дичь
        ];

        if (!$file) {
            return ['success' => false, 'error' => 1, 'error_text' => 'Не выбран файл'];
        }

        if (!empty($file['error'])) {
            return ['success' => false, 'error' => 100 + $file['error'], 'error_text' => $errorTexts[$file['error']] ?? 'Неизвестная ошибка'];
        }

        $destDirRel = rtrim("/uf/files/$destDir", '/');
        $destDirFull = $_SERVER['DOCUMENT_ROOT'] . $destDirRel;
        if (!is_dir($destDirFull)) {
            mkdir($destDirFull, 0777, true);
        }
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'error' => 2, 'error_text' => 'Не удалось загрузить файл'];
        }
        if (empty($fileName)) {
            $fileName = substr(md5(microtime()), 0, 12) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        }
        if (!move_uploaded_file($file['tmp_name'], "$destDirFull/$fileName")) {
            return ['success' => false, 'error' => 3, 'error_text' => 'Не удалось сохранить файл'];
        }
        $url = SFCore::httpProtocol(true) . "{$_SERVER['HTTP_HOST']}$destDirRel/$fileName";
        return ['success' => true, 'filename' => $fileName, 'fullpath' => $destDirFull, 'relpath' => "$destDirRel/$fileName", 'url' => $url];
    }

}
