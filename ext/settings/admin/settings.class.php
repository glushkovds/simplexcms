<?php

class AdminSettings extends AdminBase {

    public function save() {
        if ($_POST['alias'] == 'static_cache_js') {
            $this->compileJS($_POST['value'], SFAdminCore::siteParam('static_version'));
        }
        if ($_POST['alias'] == 'static_cache_css') {
            $this->compileCSS($_POST['value'], SFAdminCore::siteParam('static_version'));
        }
        if ($_POST['alias'] == 'static_version') {
            PlugFS::clearDir("{$_SERVER['DOCUMENT_ROOT']}/cache/js");
            $this->compileJS(SFAdminCore::siteParam('static_cache_js'), $_POST['value']);
            PlugFS::clearDir("{$_SERVER['DOCUMENT_ROOT']}/cache/css");
            $this->compileCSS(SFAdminCore::siteParam('static_cache_css'), $_POST['value']);
        }
        return parent::save();
    }

    protected function compileJS($value, $version) {
        $v = (int) $version;
        $lines = explode("\n", $value);
        $cache = "";
        foreach ($lines as $line) {
            $file = trim("{$_SERVER['DOCUMENT_ROOT']}$line");
            $cache .= file_get_contents($file) . "\n";
        }
        $cacheFile = "{$_SERVER['DOCUMENT_ROOT']}/cache/js/$v.js";
        $success = file_put_contents($cacheFile, $cache);
        PlugStaticMinify::js($cacheFile);
        if ($success) {
            AdminPlugAlert::success('Кэш файл обновлен успешно');
        } else {
            AdminPlugAlert::error('Ошибка! Кэш файл не обновлен');
        }
    }

    protected function compileCSS($value, $version) {
        $v = (int) $version;
        $lines = explode("\n", $value);
        $cache = "";
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue;
            }
            $file = trim("{$_SERVER['DOCUMENT_ROOT']}$line");
            $css = file_get_contents($file);
            $css = str_replace('../', dirname(dirname($line)) . '/', $css);
            $css = str_replace('(img/', '(' . dirname($line) . '/img/', $css);
            $cache .= $css . "\n";
        }
        $cacheFile = "{$_SERVER['DOCUMENT_ROOT']}/cache/css/$v.css";
        $success = file_put_contents($cacheFile, $cache);
        PlugStaticMinify::css($cacheFile);
        if ($success) {
            AdminPlugAlert::success('Кэш файл обновлен успешно');
        } else {
            AdminPlugAlert::error('Ошибка! Кэш файл не обновлен');
        }
    }

}
