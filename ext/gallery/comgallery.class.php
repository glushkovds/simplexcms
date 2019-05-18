<?php
/**
* ComGallery class
*
* PhotoGallery and Video Chanels
*
* @author Evgeny Shilov <evgeny@internet-menu.ru>
* @version 1.0
*/

class ComGallery extends SFComBase {
  private $album;
  
  public function __construct() {
    parent::__construct();
    
    SFPage::css('/ext/gallery/css/gallery.css', 99);
  }


  protected function content() {
    $action = 'main';
    if(SFCore::uri(0)) {
      $action = 'error';
      $this->album = $this->albumByAlias(SFCore::uri_r(0));
      if($this->album) {
        $action = 'album';
      }
    }
    
    switch($action) {
      case 'main'  : $this->main(); break;
      case 'album' : $this->album(); break;
      default      : SFCore::error404();
    }
  }
  
  private function main() {
    $ob = new ComContent();
    $content = $ob->get();
    $title = $content ? $content['title'] : SFCore::componentTitle();
    SFPage::seo($title);
    
    $q="SELECT album_id, name, alias, image FROM gallery_photo_album WHERE active=1 ORDER BY npp, album_id DESC";
    $albums = SFDB::assoc($q);
    
    include dirname(__FILE__).'/tpl/com_main.tpl';
  }
  
  private function album() {
    if(!SFCore::ajax()) {
      ModBreadcrumbs::add($this->album['name'], SFCore::path());
      SFPage::seo($this->album['name']);
    }
    
    $q="SELECT photo_id, name, image FROM gallery_photo_item WHERE album_id=".$this->album['album_id']." ORDER BY npp, photo_id DESC";
    $photos = SFDB::assoc($q);
    
    include dirname(__FILE__).'/tpl/com_album.tpl';
  }
  
  
  private function albumByAlias($alias) {
    $q="SELECT album_id, name, alias FROM gallery_photo_album WHERE active=1 AND alias='".SFDB::escape($alias)."'";
    return SFDB::result($q);
  }
  
  private function albums(&$albums) {
    if(count($albums)) {
      include dirname(__FILE__).'/tpl/albums.tpl';
    }
  }
  private function photos(&$photos) {
    if(count($photos)) {
      PlugJQuery::jquery();
      PlugJQuery::plugFancybox();
      PlugFrontEnd::js("$(function () { $('.ext-gallery-photos a').fancybox(); });");
      include dirname(__FILE__).'/tpl/photos.tpl';
    }
  }
  
}