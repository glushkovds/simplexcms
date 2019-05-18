<?php

class PlugNumber {

    /**
     * Форматирует число как деньги
     * @param double $value
     * @param int $decimals (default = 0)
     * @return string
     */
    public static function money($value, $decimals = 0) {
        $value = (float) @$value;
        return number_format($value, $decimals, '.', ' ');
    }

    /**
     * Форматирует число как деньги, если есть копейки, показывает, если нет, не показывает
     * @param double $value
     * @return string
     */
    public static function moneyDecimalsSmart($value) {
        $value = (float) @$value;
        $decimals = $value === (float) round($value) ? 0 : 2;
        return number_format($value, $decimals, '.', ' ');
    }

    public static function moneySup($value) {
        $ret = number_format($value, 2, '.', ' ');
        $ret = explode('.', $ret);
        $ret = $ret[0] . '.<sup>' . $ret[1] . '</sup>';
        return $ret;
    }

    /**
     * 
     * @param mixed $value
     * @return double
     */
    public static function asDouble($value, $round = false) {
        if (preg_match("@\d+\,\d{3}@", $value)) {
            $value = str_replace(',', '', $value);
        }
        $value = str_replace(array(','), array('.'), $value);
        $value = preg_replace('@[^\d\.]@', '', $value);
        $value = (double) $value;
        if ($round !== false) {
            $value = round($value, $round);
        }
        return $value;
    }

    /**
     * 
     * @param mixed $value
     * @return string|double
     */
    public static function doubleInInput($value) {
        if ($value === null || $value === '') {
            return '';
        }
        return (double) $value;
    }

}
