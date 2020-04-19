<?php

class SFConsoleBase {

    public static function toBackground($job, $params = []) {
        $tmp = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $tmp[] = '--' . $key . '="' . $value . '"';
        }
        $paramsStr = implode(' ', $tmp);
        $command = "php {$_SERVER['DOCUMENT_ROOT']}/console.php $job $paramsStr > /dev/null 2>&1 &";
        CCDebug::save($command);
        exec($command);
    }

}
