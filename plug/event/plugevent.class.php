<?php

class PlugEvent {
    
    public static function fire($name,$data){
        $exts = array_slice(scandir('ext'),2);
        foreach($exts as $ext){
            $file = "ext/$ext/event$ext.class.php";
            if(is_file($file)){
                include_once $file;
                $class = "Event".ucfirst($ext);
                if(method_exists($class, $name)){
                    $class::$name($data);
                }
            }
        }
    }
    
}
