<?php

class AdminSettings extends AdminBase
{

    public function __construct()
    {
        parent::__construct();

    }

    protected function initTable()
    {
        parent::initTable();
        if (SFUser::ican('dev')) {
            $this->fields['type']->readonly = false;
            $this->fields['enum_values']->hidden = false;
        }
    }

    public function save()
    {
        $isInsert = empty($_POST[$this->pk->name]);

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

        if ('enum' == $_POST['type'] && !$isInsert) {
            $values = static::parseEnumValues($_POST['enum_values']);
            array_key_exists($_POST['value'], $values) || $_POST['value'] = null;
        }

        return parent::save();
    }

    protected function formFieldInput($field, $row)
    {
        if (@$row['type'] == 'enum' && $field->name == 'value') {
            ob_start();
            $values = static::parseEnumValues($row['enum_values']);
            include 'tpl/form.enum.tpl';
            return ob_get_clean();
        }
        return parent::formFieldInput($field, $row);
    }

    protected static function parseEnumValues($string)
    {
        $result = [];
        foreach (explode(';;', $string) as $value) {
            $kv = explode('::', $value);
            $key = trim($kv[0]);
            $v = trim(@$kv[1]);
            if (isset($kv[1])) {
                $result[$key] = $v;
            }
        }
        return $result;
    }

    protected function compileJS($value, $version)
    {
        $v = (int)$version;
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

    protected function compileCSS($value, $version)
    {
        $v = (int)$version;
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

    public function form()
    {
        parent::form(); // TODO: Change the autogenerated stub
    }

}
