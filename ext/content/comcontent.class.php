<?php

/**
 * ComContent class
 *
 * Output content on site page
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class ComContent extends SFComBase {

    public function &get() {
        $q = "SELECT * FROM content WHERE active=1 AND path=@SITE_PATH";
        if ($content = SFDB::result($q)) {
            $content['params'] = unserialize($content['params']);
        }
        return $content;
    }

    public static function getStatic($alias) {
        $q = "SELECT * FROM content WHERE active=1 AND alias = '$alias'";
        if ($content = SFDB::result($q)) {
            $content['params'] = unserialize($content['params']);
        }
        return $content;
    }

    protected function content() {
        $content = $this->get();

        if ($content) {
            if (!SFCore::ajax()) {
                $this->breadcrumbs($content);
            }

            SFPage::seo($content['title']);

            $children = array();
            if (empty($content['params']['hide_children'])) {
                $q = "SELECT content_id, title, short, text, path, photo FROM content WHERE active=1 AND pid=" . (int) $content['content_id'];
                $children = SFDB::assoc($q);
            }
            include dirname(__FILE__) . '/tpl/com_item.tpl';
        } else {
            SFCore::error404();
        }
    }

    private function breadcrumbs($content) {
        ModBreadcrumbs::add($content['title'], $content['path']);
        $id = (int) $content['pid'];
        while ($id) {
            $q = "SELECT pid, title, path FROM content WHERE content_id=$id";
            $id = 0;
            if ($content = SFDB::result($q)) {
                ModBreadcrumbs::add($content['title'], $content['path']);
                $id = (int) $content['pid'];
            }
        }
    }

}
