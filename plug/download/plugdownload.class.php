<?php

class PlugDownload {

    /**
     * Загружает CSS для оформления списка файлов для скачивания
     */
    public static function loadCSS() {
        SFPage::css('/plug/download/download.css');
    }

    /**
     * Отправляет браузеру указанный файл
     * @param type $fpath
     * @param type $fname
     * @return string
     */
    public static function sendFile($fpath, $fname = '') {

        $ret = array();

        if (!$fpath || !file_exists($fpath)) {
            $ret['error'] = 404;
            $ret['error_text'] = 'Файл не найден!';
            return $ret;
        } else {

            $ext = self::fileExtension($fpath);
            if ($fname) {
                if (!preg_match("@\.[\w\d]{2,10}$@", $fname)) {
                    $fname .= ".$ext";
                }
            } else {
                $fname = basename($fpath);
            }

            // Determine Content Type
            switch ($ext) {
                case "pdf": $ctype = "application/pdf";
                    break;
                case "exe": $ctype = "application/octet-stream";
                    break;
                case "zip": $ctype = "application/zip";
                    break;

                case "doc":
                case "docx": $ctype = "application/msword";
                    break;
                case "xls":
                case "xlsx": $ctype = "application/vnd.ms-excel";
                    break;
                case "ppt":
                case "pptx": $ctype = "application/vnd.ms-powerpoint";
                    break;

                case "gif": $ctype = "image/gif";
                    break;
                case "png": $ctype = "image/png";
                    break;
                case "jpeg":
                case "jpg": $ctype = "image/jpg";
                    break;
                default: $ctype = "application/force-download";
            }

            // Set headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=\"$fname\"");
            header("Content-Type: $ctype");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($fpath));

            ob_end_flush();
            $fd = fopen($fpath, "rb");
            while (!feof($fd)) {
                echo fread($fd, 1 * 1024 * 1024);
            }
            fclose($fd);
            exit;
        }
    }

    /**
     * Возвращает ссылку на файл
     * @param string $filepath указываем без /uf
     * @param string $filename (optional)
     * @return string 
     */
    public static function getDownloadURL($filepath, $filename = '') {
        if (self::isImage($filepath)) {
            $ret = "/uf$filepath";
        } else {
            $ret = "/?sf_plug_name=download&file=$filepath&name=" . urlencode($filename) . "";
        }
        return $ret;
    }

    /**
     * 
     * @param string $filepath (без /uf/)
     * @return bool
     */
    public static function isImage($filepath) {
        $fullpath = self::getFullFSPath($filepath);
        $isImage = exif_imagetype($fullpath) !== false;
        return $isImage;
    }

    /**
     * Возвращает полный путь к указанному файлу. Например /files/catalog/f1.doc => /var/www/.../uf/files/catalog/f1.doc
     * @param string $filepath
     * @return string
     */
    public static function getFullFSPath($filepath) {
        $fullpath = $_SERVER['DOCUMENT_ROOT'] . (strpos($filepath, '/uf/') === false ? '/uf' : '') . $filepath;
        return $fullpath;
    }

    /**
     * Обработчик прямого обращения через URL (смотрим SFCore sf_plug_name)
     * @return void
     */
    public static function execute() {
        $path = self::getFullFSPath(@$_REQUEST['file']);
        $name = urldecode(@$_REQUEST['name']);
        if (!$path || !$name) {
            return SFCore::error404();
        }
        if (!is_file($path)) {
            return SFCore::error404();
        }
        self::sendFile($path, $name);
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
        $fullpath = self::getFullFSPath($filepath);
        $size = @(int) filesize($fullpath);
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
     * Выводит HTML с файлами
     * @param array $rows array(0 => array(file, name), 1 => ..)
     * @param string $dir Путь до файла от /uf. Напрмер: /files/catalog/
     */
    public static function showFiles($rows, $dir) {
        self::loadCSS();
        include 'files.tpl';
    }

}
