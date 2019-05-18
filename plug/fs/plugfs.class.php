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

}
