<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ModBreadcrumbs extends SFModBase {
  private static $arr = array();
  private $crumbs = array();

  protected function content() {
    $arr = array();
    $menu = SFCore::menu('by_id');
    $id = SFCore::componentMenuID();
    $curmenu = SFCore::menuCurItem();
    if(isset($menu[$curmenu['menu_id']])) {
      $id = $curmenu['menu_id'];
    }
    $is_main = false;
    while(isset($menu[$id])) {
      $arr[md5($menu[$id]['link'])] = array('name' => $menu[$id]['name'], 'link' => $menu[$id]['link']);
      if($menu[$id]['link']=='/') {
        $is_main = true;
      }
      $id = $menu[$id]['menu_pid'];
    }
    if(!$is_main) {
      $arr[md5('/')] = array('name'=>'Главная', 'link' => '/');
    }
    $this->crumbs = array_reverse($arr);
    
    $arr = array_reverse(self::$arr);
    $this->crumbs = array_merge($this->crumbs, $arr);

    $cnt = count($this->crumbs);
    if($cnt > 1) {
      $crumbs = array();
      $i=0;
      foreach($this->crumbs as $crumb) {
        $crumbs[] = $i+1<$cnt ? '<a href="'.$crumb['link'].'">'.$crumb['name'].'</a>' : '<span>'.$crumb['name'].'</span>';
        $i++;
      }
      include dirname(__FILE__).'/breadcrumbs.tpl';
    }
  }
  public static function add($name, $link) {
    self::$arr[md5($link)] = array('name'=>$name, 'link' => $link);
  }
}
