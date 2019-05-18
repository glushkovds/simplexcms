<?php

/**
 * PlugFrontEnd class
 *
 * Allow to output javascript and css from any programm point
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class PlugFunc {

    public static function dateFormat($date) {
        return substr($date, -2) . '.' . substr($date, 5, 2) . '.' . substr($date, 0, 4);
    }

    public static function translite($string) {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => "'", 'ы' => 'y', 'ъ' => "'",
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => "'", 'Ы' => 'Y', 'Ъ' => "'",
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $string = strtr($string, $converter);
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('@\-+@', '-', $string);
        $string = preg_replace('@[^a-z0-9\-]@i', '', $string);
        $string = strtolower($string);
        return trim($string);
    }

    public function emailCheck($email) {
        return preg_match('/^[a-z0-9_\-\.]{1,20}@[a-z0-9\-\.]{1,20}\.[a-z]{2,8}$/i', $email);
    }

    /**
     * Рекурсивно конвертирует массив или строку. У массива ключи тоже конвертируются
     * @param string|array $mixed
     * @return string|array
     */
    public static function utf2win($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $str) {
                unset($mixed[$index]);
                $mixed[self::utf2win($index)] = self::utf2win($str);
            }
            return $mixed;
        }
        return iconv('utf-8', 'windows-1251//TRANSLIT', $mixed);
    }

    /**
     * Рекурсивно конвертирует массив или строку. У массива ключи тоже конвертируются
     * @param string|array $mixed
     * @return string|array
     */
    public static function win2utf($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $str) {
                unset($mixed[$index]);
                $mixed[self::win2utf($index)] = self::win2utf($str);
            }
            return $mixed;
        }
        return iconv('windows-1251', 'utf-8', $mixed);
    }

    /**
     * Функция убирает все html-теги из строки
     * @param bool $removeHTMLComments - удалять или нет HTML комменты из текста
     * @param string $html 
     */
    public static function plainText($html, $removeHTMLComments = false) {

        if ($removeHTMLComments)
            $html = preg_replace('/<!--(.*)-->/Uis', '', $html);

        $search_str = array("'<script[^>]*?>.*?</script>'si", // Strip out javascript
            "'<[/!]*?[^<>]*?>'si", // Strip out HTML tags
            "'([rn])[s]+'", // Strip out white space
            "'&(quot|#34);'i", // Replace HTML entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(d+);'e");                    // evaluate as php

        $replace_str = array("",
            "",
            "\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\1)");

        $ret = preg_replace($search_str, $replace_str, $html);

        // подчищаем неудаленные части тэгов
        $ret = strip_tags($ret);

        return $ret;
    }

    /**
     * 
     * @param string $url
     */
    public static function asyncGet($url) {
        $parts = parse_url($url);
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
        $out = "GET " . $parts['path'] . "?{$parts['query']} HTTP/1.1\r\n";
        $out.= "Host: " . $parts['host'] . "\r\n";
        $out.= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        fclose($fp);
    }

    /**
     * Сортировка по полю массива
     * @param array &$array
     * @param string $field Сортировка по этому полю
     * @param int $type (optional = SORT_STRING)
     * @param int $direction (optional = SORT_ASC)
     * @return bool
     */
    public static function sortByField(&$array, $field, $type = false, $direction = false) {
        $type === false && $type = SORT_STRING;
        $direction === false && $direction = SORT_ASC;
        $result = usort($array, function($a, $b) use ($field, $type, $direction) {
            if (SORT_STRING == $type) {
                $ret = strcmp($a[$field], $b[$field]);
            } elseif (SORT_NUMERIC == $type) {
                $ret = (double) $a[$field] >= (double) $b[$field];
            }
            return $direction == SORT_ASC ? $ret : !$ret;
        });
        return $result;
    }

    public static function wrapStatic($url) {
        $std = SFCore::siteParam('static_domain');
        $sub = SFCore::siteParam('static_domain_sub') ? 'media.' : '';
        if ($std && strpos($url, 'http') === false) {
            $url = "http://$sub$std$url";
        }
        return $url;
    }

}
