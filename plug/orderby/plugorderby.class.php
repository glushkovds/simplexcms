<?php

class PlugOrderBy {
    
    private static $data;
    
    /**
     * 
     * @param array $fields<br> array(<br>
     *      array('name' => 'Имя', 'sysname' => 'name', 'default_direction'(optional=asc) => desc, 'is_default'(optional) ),<br>
     *      array('name' => ... )<br>
     * )
     */
    public static function setFields($fields){
        $defaultField = '';
        $defaultDirection = 'asc';
        $actualFields = array();
        $directions = array('asc','desc');
        
        foreach($fields as $field){
            if(!isset($field['default_direction'])){
                $field['default_direction'] = 'asc';
            }
            if(in_array('is_default',$field)){
                $defaultField = $field['sysname'];
                $defaultDirection = $field['default_direction'];
            }
            $field['next_direction'] = $field['default_direction'];
            $actualFields[$field['sysname']] = $field;
        }
        
        $currentField = $defaultField;
        $currentDirection = $defaultDirection;
        
        if(isset($_GET['orderby'])){
            $tmp = explode(':',$_GET['orderby']);
            if(isset($actualFields[$tmp[0]])){
                $currentField = $tmp[0];
            }
            if(isset($tmp[1]) && in_array($tmp[1], $directions)){
                $currentDirection = $tmp[1];
                $directionsFlip = array_flip($directions);
                $nd = $directions[($directionsFlip[$currentDirection]+1)%count($directions)];
                $actualFields[$currentField]['next_direction'] = $nd;
            }
        }
        
        
        self::$data = array(
            'fields' => $actualFields, 'deff' => $defaultField, 'defd' => $defaultDirection,
            'curf' => $currentField, 'curd' => $currentDirection
        );
    }
    
    public static function sqlStr(){
        if(self::$data['curf']){
            return 'order by '.self::$data['curf'].' '.self::$data['curd'];
        }
        return '';
    }

    public static function html(){
        SFPage::js('/plug/orderby/orderby.js');
        include 'orderby.tpl';
    }
    
}