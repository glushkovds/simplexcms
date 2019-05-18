<?php

/**
 * 
 * Usage: 
 * 1. Specify log dir path in /config.php For example: /var/log/site.com
 * 2. Put in code (any place, admin/site/cron): SFLog::warning('Ouch!'); or SFLog::debug(['id'=>1,'price'=2]);
 * 3. This will put to /var/log/site.com/YYYY-MM-DD.log
 * 
 * @method void debug(mixed $content) Push debug record
 * @method void info(mixed $content) Push info record
 * @method void warning(mixed $content) Push warn record
 * @method void error(mixed $content) Push error record
 */
class SFLog {

    private static $levels = ['disabled' => -1, 'error' => 0, 'warning' => 1, 'info' => 2, 'debug' => 3];

    private static function getFileName($suffix = '') {
        if (!is_dir($rootDir = empty(SFConfig::$logPath) ? __DIR__ . '../log' : SFConfig::$logPath)) {
            if (!@mkdir($rootDir, 0700, true)) {
                return null;
            }
        }
        return $rootDir . '/' . date('Y-m-d') . ($suffix ? "_$suffix" : '') . '.log';
    }

    private static function contentToStr($content) {
        if (is_array($content)) {
            $content = print_r($content, true);
        }
        if (is_object($content)) {
            $content = print_r($content, true);
        }
        str_replace("\n", ' ', $content);
        return $content;
    }

    private static function buildStr($content, $level) {
        return date('Y-m-d H:i:s') . " [$level] " . self::contentToStr($content);
    }

    private static function write($content, $level) {
        if (self::$levels[$level] > self::$levels[empty(SFConfig::$logLevel) ? 'error' : SFConfig::$logLevel]) {
            return;
        }
        $str = self::buildStr($content, $level) . "\n";
        if ('error' == $level) {
            error_log(self::contentToStr($content));
        }
        if ($fname = self::getFileName()) {
            file_put_contents($fname, $str, FILE_APPEND);
        }
    }

    public static function __callStatic($name, $arguments) {
        if (in_array($name, self::$levels)) {
            self::write($arguments[0], $name);
        }
    }

}
