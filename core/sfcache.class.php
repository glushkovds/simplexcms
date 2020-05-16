<?php

class SFCache
{

    protected static $dir;

    protected static function getFilePath($key)
    {
        if (empty(static::$dir)) {
            static::$dir = "{$_SERVER['DOCUMENT_ROOT']}/uf/files/cache";
            if (!is_dir(static::$dir)) {
                if (!@mkdir(static::$dir, 0777, true)) {
                    throw new Exception('Cannot create cache dir ' . static::$dir);
                }
            }
        }
        return static::$dir . '/' . static::encodeKey($key);
    }

    protected static function encodeKey($key)
    {
        return preg_match('@^[\w\d\.\-\_]{1,24}$@i', $key) ? $key : md5($key);
    }

    /**
     * @param string $key
     * @param callable $callback
     * @param int $expires Cache value validity in seconds
     * @return mixed
     * @throws Exception
     */
    public static function getOrSet(string $key, callable $callback, int $expires = 0)
    {
        $value = static::get($key);
        if (is_null($value)) {
            $value = $callback();
            static::set($key, $value, $expires);
            return $value;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expires
     * @throws Exception
     */
    public static function set(string $key, $value, int $expires = 0)
    {
        $time = $expires > 0 ? time() + $expires : 0;
        $contents = array_merge([$time], static::serialize($value));
        if (!@file_put_contents($fp = static::getFilePath($key), implode("\n", $contents))) {
            throw new Exception("Cache file $fp is not writable");
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public static function get(string $key)
    {
        $fp = static::getFilePath($key);
        if (!is_file($fp)) {
            return null;
        }
        $contents = explode("\n", file_get_contents($fp), 3);
        if (
            count($contents) !== 3
            || ($til = $contents[0]) && !($til = (int)$til)
            || !in_array($type = $contents[1], ['numeric', 'string', 'array', 'object', 'null', 'bool'])
        ) {
            throw new Exception("Broken cache file $fp");
        }
        if ($til && $til < time()) {
            unlink($fp);
            return null;
        }
        $value =& $contents[2];

        if ('numeric' == $type) {
            return (double)$value;
        }
        if ('string' == $type) {
            return $value;
        }
        if ('array' == $type) {
            return json_decode($value, true);
        }
        if ('object' == $type) {
            return unserialize($value);
        }
        if ('null' == $type) {
            return null;
        }
        if ('bool' == $type) {
            return (bool)$value;
        }
    }

    protected static function serialize($value)
    {
        if (is_numeric($value)) {
            return ['numeric', $value];
        }
        if (is_bool($value)) {
            return ['bool', (int)$value];
        }
        if (is_string($value)) {
            return ['string', $value];
        }
        if (is_array($value)) {
            return ['array', json_encode($value, JSON_UNESCAPED_UNICODE)];
        }
        if (is_object($value)) {
            return ['object', self::serialize($value)];
        }
        return ['null', ''];
    }

}