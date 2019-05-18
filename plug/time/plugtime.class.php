<?php

class PlugTime {

    /**
     * Время объекта в секундах
     * @var int
     */
    private $time = 0;
    public static $wdays = array('ВС', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ');
    public static $wdaysFull = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
    public static $wdaysFullLC = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
    public static $months = array('', 'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
    public static $monthsDat = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    public static $monthsFull = array('', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
    public static $monthsFullUCF = array('', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');

    /**
     * Индекс января = 1
     * @var type 
     */
    public static $daysInMonth = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    public static function getMonths() {
        return array(
            1 => array('name' => 'Январь', 'days' => 31),
            2 => array('name' => 'Февраль', 'days' => 28),
            3 => array('name' => 'Март', 'days' => 31),
            4 => array('name' => 'Апрель', 'days' => 30),
            5 => array('name' => 'Май', 'days' => 31),
            6 => array('name' => 'Июнь', 'days' => 30),
            7 => array('name' => 'Июль', 'days' => 31),
            8 => array('name' => 'Август', 'days' => 31),
            9 => array('name' => 'Сентябрь', 'days' => 30),
            10 => array('name' => 'Октябрь', 'days' => 31),
            11 => array('name' => 'Ноябрь', 'days' => 30),
            12 => array('name' => 'Декабрь', 'days' => 31)
        );
    }

    public function __construct($time = false) {
        if ($time === false) {
            $time = time();
        }
        $time = (int) self::unix($time);
        $this->time = $time;
    }

    /**
     * Создает объект класса
     * @return PlugTime
     */
    public static function create($time = false) {
        return new static($time);
    }

    /**
     * Добавляет к дате интервал
     * @param string|int $interval - или строка типа "+1 year" или число секунд (3600)
     * @return PlugTime
     */
    public function add($interval) {
        if (strpos($interval, " ")) {
            $this->time = strtotime($interval, $this->time);
        } elseif ((int) $interval) {
            $this->time += (int) $interval;
        }
        return $this;
    }

    /**
     * Вернуть в формате GMT
     * @return string
     */
    public function asGMT() {
        return self::convert($this->time, 'gmt');
    }

    /**
     * Вернуть в формате unixtime, в секундах
     * @return int
     */
    public function asUnix() {
        return $this->time;
    }

    /**
     * Вернуть в формате MySQL
     * @param bool (default: auto) $showHourMin - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: false) $showSec - Показывать :секунды. Не имеет эффекта с неустановленным $showHourMin.
     * @return string
     */
    public function asMySQL($showHourMin = 'auto', $showSec = false) {
        return self::convert($this->time, 'Y-m-d H:i:s', $showHourMin, $showSec);
    }

    /**
     * Вернуть в читаемом формате: 31.12.1999 00:00:00
     * @param bool (default: auto) $showHourMin - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: false) $showSec - Показывать :секунды. Не имеет эффекта с неустановленным $showHourMin.
     * @return string
     */
    public function asNormal($showHourMin = 'auto', $showSec = false) {
        return self::convert($this->time, 'd.m.Y H:i:s', $showHourMin, $showSec);
    }

    /**
     * Вернуть в читаемом формате: 31 янв 1999 00:00:00
     * @param bool (default: auto) $showHourMin - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: false) $showSec - Показывать :секунды. Не имеет эффекта с неустановленным $showHourMin.
     * @return string
     */
    public function asUser($showHourMin = 'auto', $showSec = false) {
        return self::convert($this->time, 'd K Y H:i:s', $showHourMin, $showSec);
    }

    /**
     * Вернуть в читаемом формате: 31 января 1999 00:00:00
     * @param bool (default: auto) $showHourMin - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: false) $showSec - Показывать :секунды. Не имеет эффекта с неустановленным $showHourMin.
     * @return string
     */
    public function asUserFull($showHourMin = 'auto', $showSec = false) {
        return self::convert($this->time, 'd Q Y H:i:s', $showHourMin, $showSec);
    }

    /**
     * Вернуть год-месяц 1999-01
     * @return string
     */
    public function asYearMonth() {
        return self::convert($this->time, 'Y-m', false);
    }

    /**
     * Форматирует в нужный формат время
     * @param string $format - Формат возвращаемого времени. Дополнительно: <b>X</b> = полный месяц по русски в именительном падеже.
     * <b>R</b> = 2 букв. русский день недели. <b>J</b> = полный русский день недели маленькие буквы<br/>
     * <b>Q</b> = полный месяц по русски в дательном падеже, <b>E</b> = полный месяц по русски в именительном падеже, первая буква заглавная
     * <b>K</b> = 3 буквы месяца
     * Например "Y-m-d"
     * @return mixed 
     */
    public function format($format) {
        return self::convert($this->time, $format, false);
    }

    public function __toString() {
        return $this->asMySQL();
    }

    /**
     * Определяет формат
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @return string: normal, mysql, excel, unix
     */
    public static function what_format($value) {
        if (strpos(' ' . $value, 'T')) {
            $from = 'gmt';
        } elseif (strpos(' ' . $value, '.')) {
            $from = 'normal';
        } elseif (strpos(' ' . $value, '-')) {
            $from = 'mysql';
        } elseif (strpos(' ' . $value, '/')) {
            $from = 'excel';
        } elseif (strpos(' ' . $value, ':')) {
            $from = 'time';
        } else {
            $from = 'unix';
        }
        return $from;
    }

    /**
     * Преобразовывает время в Читаемый формат (12 янв 2012 12:23:34)
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @param bool (default: auto) $show_hour_min - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: auto) $show_sec - Показывать :секунды. Не имеет эффекта с неустановленным $show_hour_min.
     * По умолчанию если в исходном формате есть, то будет показано
     * @return type 
     */
    public static function user($value, $show_hour_min = 'auto', $show_sec = false) {
        $ret = self::convert($value, "d.m.Y", $show_hour_min, $show_sec);
        $month = self::convert($value, "m", false);
        $ret = str_replace(" ", " в ", $ret);
        $ret = str_replace(".$month.", " " . self::$months[(int) $month] . " ", $ret);
        return $ret;
    }

    /**
     * Преобразовывает время в Читаемый формат (12 января 2012 12:23:34)
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @param bool (default: auto) $show_hour_min - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: auto) $show_sec - Показывать :секунды. Не имеет эффекта с неустановленным $show_hour_min.
     * По умолчанию если в исходном формате есть, то будет показано
     * @return type 
     */
    public static function userfull($value, $show_hour_min = 'auto', $show_sec = false) {
        $ret = self::convert($value, "d.m.Y", $show_hour_min, $show_sec);
        $month = self::convert($value, "m", false);
        $ret = str_replace(" ", " в ", $ret);
        $ret = str_replace(".$month.", " " . self::$monthsDat[(int) $month] . " ", $ret);
        if (!strpos($ret, ":"))
            $ret .= " года";
        return $ret;
    }

    /**
     * Преобразовывает время в mysql-формат
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @param bool (default: auto) $show_hour_min - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: auto) $show_sec - Показывать :секунды. Не имеет эффекта с неустановленным $show_hour_min.
     * По умолчанию если в исходном формате есть, то будет показано
     * @return type 
     */
    public static function mysql($value, $show_hour_min = 'auto', $show_sec = 'auto') {
        return self::convert($value, "Y-m-d", $show_hour_min, $show_sec);
    }

    /**
     * Преобразовывает время в Читаемый формат (normal)
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @param bool (default: auto) $show_hour_min - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: auto) $show_sec - Показывать :секунды. Не имеет эффекта с неустановленным $show_hour_min.
     * По умолчанию если в исходном формате есть, то будет показано
     * @return type 
     */
    public static function normal($value, $show_hour_min = 'auto', $show_sec = 'auto') {
        return self::convert($value, "d.m.Y", $show_hour_min, $show_sec);
    }

    /**
     * Возвращает временную метку в формате unix (пример: 1318401180)
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @return int 
     */
    public static function unix($value) {
        $format = self::what_format($value);
        switch ($format) {
            case 'normal':

                if (strlen($value) == 10)
                    $value.=' 00:00:00';
                if (strlen($value) == 16)
                    $value.=':00';

                preg_match("/(\d{2}).(\d{2}).(\d{4}) (\d{1,2}):(\d{2}):(\d{2})/", $value, $time_ar);
                return mktime($time_ar[4], $time_ar[5], $time_ar[6], $time_ar[2], $time_ar[1], $time_ar[3]);

                break;
            case 'gmt':
                return strtotime($value);
                break;
            case 'mysql':

                strlen($value) == 7 ? $value .= '-01' : null;
                strlen($value) == 10 ? $value .= ' 00:00:00' : null;
                strlen($value) == 16 ? $value .= ':00' : null;

                preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $value, $time_ar);
                if(count($time_ar) < 6 && imDev()){
                    echo $value;
                    print_r($time_ar);
                    debug_print_backtrace();
                    die;
                }
                return mktime($time_ar[4], $time_ar[5], $time_ar[6], $time_ar[2], $time_ar[3], $time_ar[1]);

                break;
            case 'excel':

                if (strlen($value) == 10)
                    $value.=' 00:00:00';
                if (strlen($value) == 16)
                    $value.=':00';

                preg_match("/(\d{2})\/(\d{2})\/(\d{4}) (\d{1,2}):(\d{2}):(\d{2})/", $value, $time_ar);
                return mktime($time_ar[4], $time_ar[5], $time_ar[6], $time_ar[2], $time_ar[3], $time_ar[1]);

                return 0;
                break;
            case 'time':
                $tmp = explode(':', $value);
                if (count($tmp) == 3) {
                    $value = (int) $tmp[0] * 60 * 60 + (int) $tmp[1] * 60 + (int) $tmp[2];
                } elseif (count($tmp) == 2) {
                    $value = (int) $tmp[0] * 60 * 60 + (int) $tmp[1] * 60;
                } else {
                    $value = 0;
                }
                return $value;
            case 'unix':
                return $value;
                break;
            default: return 0;
        }
    }

    /**
     * Форматирует в нужный формат время
     * @param mixed $value - Время в любом доступном формате: normal, mysql, excel, unix
     * @param string $format - Формат возвращаемого времени. Дополнительно: <b>X</b> = полный месяц по русски в именительном падеже.
     * <b>R</b> = 2 букв. русский день недели. <b>J</b> = полный русский день недели маленькие буквы<br/>
     * <b>Q</b> = полный месяц по русски в дательном падеже, <b>E</b> = полный месяц по русски в именительном падеже, первая буква заглавная
     * <b>K</b> = 3 буквы месяца
     * Например "Y-m-d"
     * 
     * @param bool (default: auto) $show_hour_min - Показывать часы:минуты. По умолчанию если в исходном формате есть, то будет показано
     * @param bool (default: auto) $show_sec - Показывать :секунды. Не имеет эффекта с неустановленным $show_hour_min.
     * По умолчанию если в исходном формате есть, то будет показано
     * @return mixed 
     */
    public static function convert($value, $format, $show_hour_min = 'auto', $show_sec = 'auto') {

        if ($value == '-' || $value == '' || $value == ' - ')
            return $value;

        $unix = self::unix($value);

        $sourceFormat = self::what_format($value);
        if ($sourceFormat == 'time') {
            return gmdate($format, $unix);
        }

        if (strpos($format, "H") === false) {
            $from = self::what_format($value);
            $tmp = explode(":", $value);
            $has_hm = count($tmp) > 1;
            $has_s = count($tmp) == 3;
            if (
                $has_hm && $show_hour_min == 'auto' ||
                $show_hour_min === true ||
                $from == 'unix' && $show_hour_min == 'auto'
            ) {
                $format .= " H:i";
                if (
                    $has_s && $show_sec == 'auto' ||
                    $show_sec === true ||
                    $from == 'unix' && $show_sec == 'auto'
                )
                    $format .= ":s";
            }
        }

        $replace = array();
        if (strpos($format, "X") !== false) {
            $format = str_replace("X", " @#m#@ ", $format);
            $replace[] = "X";
        }
        if (strpos($format, "Q") !== false) {
            $format = str_replace("Q", " @@#m#@@ ", $format);
            $replace[] = "Q";
        }
        if (strpos($format, "E") !== false) {
            $format = str_replace("E", " @~@#m#@~@ ", $format);
            $replace[] = "E";
        }
        if (strpos($format, "R") !== false) {
            $format = str_replace("R", " @-#w#-@ ", $format);
            $replace[] = "R";
        }
        if (strpos($format, "J") !== false) {
            $format = str_replace("J", " @--#w#--@ ", $format);
            $replace[] = "J";
        }
        if (strpos($format, "K") !== false) {
            $format = str_replace("K", " @-_-#m#-_-@ ", $format);
            $replace[] = "K";
        }

        $ret = date($format, $unix);

        if (count($replace)) {
            foreach ($replace as $L) {
                if ("X" == $L) {
                    $ret = preg_replace_callback(
                        '/ \@\#(\d+)\#\@ /i', create_function('$matches', 'return ' . get_class() . '::$monthsFull[(int)$matches[1]];'), $ret
                    );
                }
                if ("Q" == $L) {
                    $ret = preg_replace_callback(
                        '/ \@\@\#(\d+)\#\@\@ /i', create_function('$matches', 'return ' . get_class() . '::$monthsDat[(int)$matches[1]];'), $ret
                    );
                }
                if ("E" == $L) {
                    $ret = preg_replace_callback(
                        '/ \@\~\@\#(\d+)\#\@\~\@ /i', create_function('$matches', 'return ' . get_class() . '::$monthsFullUCF[(int)$matches[1]];'), $ret
                    );
                }
                if ("R" == $L) {
                    $ret = preg_replace_callback(
                        '/ \@\-\#(\d+)\#\-\@ /i', create_function('$matches', 'return ' . get_class() . '::$wdays[(int)$matches[1]];'), $ret
                    );
                }
                if ("J" == $L) {
                    $ret = preg_replace_callback(
                        '/ \@\-\-\#(\d+)\#\-\-\@ /i', create_function('$matches', 'return ' . get_class() . '::$wdaysFullLC[(int)$matches[1]];'), $ret
                    );
                }
                if ("K" == $L) {
                    $ret = preg_replace_callback(
                        '/ \@\-_\-\#(\d+)\#\-_\-\@ /i', create_function('$matches', 'return ' . get_class() . '::$months[(int)$matches[1]];'), $ret
                    );
                }
            }
        }
        return $ret;
    }

    /**
     * Вычисление времени между двумя датами
     * @param mixed $value1 - из которого вычитаем
     * @param mixed $value2 - которое вычитаем
     * @param string $to - формат времени на выходе
     * @param boolean $show_sec - выводить секунды или нет
     * @return type 
     */
    public static function sub_times($value1, $value2, $to = 'unix', $show_sec = true) {
        if ($value1 != '-' && $value2 != '-') {

            $res_time = self::unix($value1) - self::unix($value2);

            if ($to == 'unix')
                return $res_time;
            if ($to == 'normal')
                return self::sub_time($res_time, $show_sec);
        } else
            return '-';
    }

    /**
     * вход: 129; выход 02:09
     * @param int $res_time
     * @param boolean $show_sec - выводить секунды или нет
     * @return string
     */
    public static function sub_time($res_time, $show_sec = true) {
        if ($res_time > 0) {
            $res_time = round($res_time);

            $hour = (int) floor($res_time / 3600);
            $minute = (int) floor(($res_time - $hour * 3600) / 60);
            $second = $res_time - $hour * 3600 - $minute * 60;

            if ($hour >= 0 && $hour < 10)
                $hour = '0' . $hour;
            if ($minute >= 0 && $minute < 10)
                $minute = '0' . $minute;
            if ($second >= 0 && $second < 10)
                $second = '0' . $second;

            $ret = "$hour:$minute";
            if ($show_sec)
                $ret .= ":$second";

            return $ret;
        } else
            return 0;
    }

    /**
     * вход: 02:09; выход 129
     * @param string $value
     * @return int
     */
    public static function unixtimeFromTime($value) {
        if (!$value) {
            return $value;
        }
        $res = array();
        preg_match("/([0-9]*:)?([0-9]+):([0-9]+)/", $value, $res);
        $unixtime = intval($res[1]) * 3600 + $res[2] * 60 + $res[3];
        return $unixtime;
    }

    /**
     * ФУНКЦИЯ ФОРМИРОВАНИЯ МАССИВА ДАТ ПО ДАТЕ НАЧАЛА, ДАТЕ КОНЦА И ДЕЛИТЕЛЮ
     *      Пример:

      Вход:
      $begin_date = 2008-01-01 00:00:00;
      $end_date = 2008-03-00 00:00:00;
      $delimiter = 1;

      $delimiter принимает значения:
      1 - Месяц
      2 - Квартал
      3 - Год

      Входные даты могут быть в 3 формах:
      normal - 01.01.2008 00:00:00
      mysql - 2008-03-00 00:00:00
      unix - 1200123412456

      Выход:
      Array
      (
      [0] => Array
      (
      [period] => Январь 2008
      [period_sys] => 01.2008
      [isnow] => boolean
      [beg] => 2008-01-01 00:00:00
      [end] => 2008-01-31 23:59:59
      )

      [1] => Array
      (
      [period] => Февраль 2008
      [period_sys] => 02.2008
      [isnow] => boolean
      [beg] => 2008-02-01 00:00:00
      [end] => 2008-02-31 23:59:59
      )
      )
     * @global type $months
     * @param type $begin_date
     * @param type $end_date
     * @param type $delimiter
     * @return string 
     */
    public static function form_dates($begin_date, $end_date, $delimiter) {

        $beg_date = self::mysql($begin_date, true, true);
        $end_date = self::mysql($end_date, true, true);

        $beg_date_u = self::unix($begin_date);
        $end_date_u = self::unix($end_date);
        $beg_year = self::convert($begin_date, "Y");
        $end_year = self::convert($end_date, "Y");
        $beg_month = self::convert($begin_date, "m");
        $end_month = self::convert($end_date, "m");
        $ret = array();

        switch ($delimiter) {
            case 1:
                $cnt = floor(($end_date_u - $beg_date_u) / 2678400) + 1;
                for ($i = 0; $i < $cnt; $i++) {
                    $y = $beg_year + (floor(($beg_month - 1 + $i) / 12));
                    $mi = ($beg_month - 1 + $i % 12) % 12;
                    $m = self::$monthsFull[$mi + 1];
                    $ret[$i]['period'] = $m . ' ' . $y;
                    $ret[$i]['period_sys'] = ($mi + 1 > 9 ? $mi + 1 : "0" . ($mi + 1)) . '.' . $y;
                    $ret[$i]['isnow'] = (int) date("m") == $mi + 1 && (int) date("Y") == $y;
                    if ($mi < 9)
                        $mi = '0' . ($mi + 1);
                    $ret[$i]['beg'] = $y . '-' . $mi . '-01 00:00:00';
                    $ret[$i]['end'] = $y . '-' . $mi . '-31 23:59:59';
                }
                break;
            case 2:
                $cnt = floor(($end_date_u - $beg_date_u) / 8035200) + 1;
                if ($end_month - $beg_month <= 3 && (($beg_month - 1) % 3) != 0)
                    $cnt++;
                for ($i = 0; $i < $cnt; $i++) {
                    $y = $beg_year + (floor((floor(($beg_month - 1) / 3) + $i) / 4));
                    $qi = (floor(($beg_month - 1) / 3) + $i % 4) % 4 + 1;
                    $ret[$i]['period'] = $qi . ' квартал ' . $y;
                    $mib = self::zero($qi * 3 - 3);
                    $mie = self::zero($qi * 3);
                    $ret[$i]['beg'] = $y . '-' . $mib . '-01 00:00:00';
                    $ret[$i]['end'] = $y . '-' . $mie . '-31 23:59:59';
                }
                break;
            case 3:
                for ($i = $beg_year; $i <= $end_year; $i++) {
                    $ret[$i - $beg_year]['period'] = $i;
                    $ret[$i - $beg_year]['beg'] = $i . '-01-01 00:00:00';
                    $ret[$i - $beg_year]['end'] = $i . '-12-31 23:59:59';
                }
                break;
        }

        return $ret;
    }

    protected static $timestamp;

    public static function startTimer() {
        self::$timestamp = microtime(1);
    }

    public static function getExecTime() {
        $precision = 3;
        $pagetime = round(microtime(1) - self::$timestamp, $precision);
        $pagetime = number_format($pagetime, $precision, '.', '');
        if ($pagetime > 60)
            $pagetime = self::sub_time($pagetime);
        return $pagetime;
    }

    /**
     * Возвращает день недели в двухбуквенном формате (ПН, ВТ, СР...)
     * @param mixed $value
     * @return string
     */
    public static function wday($value) {
        $unix = self::unix($value);
        $wday = self::$wdays[date("w", $unix)];
        return $wday;
    }

    /**
     * Возвращает день недели полностью
     * @param mixed $value
     * @return string
     */
    public static function wdayFull($value) {
        $unix = self::unix($value);
        $wday = self::$wdaysFull[date("w", $unix)];
        return $wday;
    }

    /**
     * Возвращает время из даты
     * @param int|string $value - Датавремя в любом допустимом формате
     * @param bool (optional = auto) $show_sec
     * @return string 12:34 или 12:34:56
     */
    public static function extractTime($value, $show_sec = 'auto') {
        $value = self::mysql($value, 'auto', $show_sec);
        $value = self::unix($value);
        $format = $show_sec ? "H:i:s" : "H:i";
        $time = date($format, $value);
        return $time;
    }

    /**
     * Добавляет к дате интервал, сохраняя формат даты
     * @param string|int $value
     * @param string|int $interval - или строка типа "+1 year" или число секунд (3600)
     * @param bool (optional = false) $toUnix - перевести в unix формат или нет. Со значением true отработает быстрее
     * @return string|int 
     */
    public static function addInterval($value, $interval, $toUnix = false) {
        $format = self::what_format($value);
        $unix = self::unix($value);

        if (strpos($interval, " ")) {
            $unix = strtotime($interval, $unix);
            return is_callable("self::$format") && !$toUnix ? self::$format($unix) : self::unix($unix);
        } elseif ((int) $interval) {
            $unix += (int) $interval;
            return is_callable("self::$format") && !$toUnix ? self::$format($unix) : self::unix($unix);
        }
        return $value;
    }

    /**
     * Вычисляет разницу между двумя датами
     * @param string|int|PlugTime $value
     * @param bool $dropTime [optional = false] не учитывать часы минуты
     * @return \ZPlugTimeDiff
     */
    public function diff($value, $dropTime = false) {
        $shoHourMin = $dropTime ? false : 'auto';
        $datetime1 = new DateTime($this->asMySQL($shoHourMin));
        $dd = $value instanceof self ? $value->asMySQL($shoHourMin) : self::mysql($value, $shoHourMin);
        $datetime2 = new DateTime($dd);
        $interval = $datetime1->diff($datetime2);
        return new ZPlugTimeDiff($interval);
    }

}

class ZPlugTimeDiff {

    private $value;

    /**
     * 
     * @param DateInterval $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    public function __toString() {
        return (string) $this->value->days;
    }

    /**
     * @see http://php.net/manual/ru/dateinterval.format.php
     * @param string $format %d - дни, %Y - года, %H - часы и т.п.
     * @return string
     */
    public function format($format) {
        return $this->value->format($format);
    }

    /**
     * Возвращает строковое представление
     * @param int $ss Количество значимых значений
     * @return string 2 дня 3 часа 40 минут
     */
    public function significant($ss = 3) {
        $ret = array();
        $this->value->y && $ret[] = $this->value->y . ' ' . PlugDeclension::byCount($this->value->y, 'год', 'года', 'лет');
        $this->value->m && $ret[] = $this->value->m . ' ' . PlugDeclension::byCountMonths($this->value->m);
        $this->value->d && $ret[] = $this->value->d . ' ' . PlugDeclension::byCountDays($this->value->d);
        $this->value->h && $ret[] = $this->value->h . ' ' . PlugDeclension::byCount($this->value->h, 'час', 'часа', 'часов');
        $this->value->i && $ret[] = $this->value->i . ' ' . PlugDeclension::byCount($this->value->i, 'минута', 'минуты', 'минут');
        $this->value->s && $ret[] = $this->value->s . ' ' . PlugDeclension::byCount($this->value->s, 'секунда', 'секунды', 'секунд');
        $ret = array_slice($ret, 0, $ss);
        return implode(' ', $ret);
    }

}
