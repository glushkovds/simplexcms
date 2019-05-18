<?php

class PlugCache {

    /**
     * 
     * @param string $key
     * @param string|array $value Максимальная длина 65535. - Это максимальная длина MySQL VARCHAR. На некоторых серверах длина еще меньше.
     * @param int $expires [optional = 3600] В секундах
     * @return bool success
     */
    public static function set($key, $value = null, $expires = null) {
        if (empty($value)) {
            $q = "DELETE FROM plug_cache WHERE cache_key = '" . SFDB::escape($key) . "'";
        } else {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $set = ["cache_key = '" . SFDB::escape($key) . "', value = '" . SFDB::escape($value) . "'"];
            if ($expires) {
                $set[] = "expires = " . (int) $expires;
            }
            $q = "REPLACE INTO plug_cache SET " . implode(', ', $set);
        }
        $success = SFDB::query($q);
        // Такой мусоросборщик
        if (rand(1, 10) == 1) {
            $q = "DELETE FROM plug_cache WHERE time_create + INTERVAL expires SECOND > NOW()";
            SFDB::query($q);
        }
        return $success;
    }

    /**
     * 
     * @param string $key
     * @param mixed $defaultValue
     * @return string|array
     */
    public static function get($key, $defaultValue = null) {
        $q = "SELECT * FROM plug_cache WHERE cache_key = '" . SFDB::escape($key) . "' AND time_create + INTERVAL expires SECOND > NOW()";
        if ($row = SFDB::result($q)) {
            $ret = $row['value'];
            if ($json = @json_decode($ret, true)) {
                return $json;
            }
            return $ret;
        }
        return $defaultValue;
    }

}
