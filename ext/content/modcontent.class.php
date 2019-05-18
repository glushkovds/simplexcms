<?php

/**
 * ModContent class
 *
 * Output last contents
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class ModContent extends SFModBase {

    protected function content() {
        $content_id = empty($this->params['content_id']) ? 0 : (int) $this->params['content_id'];
        $cnt_limit = empty($this->params['cnt_limit']) ? 0 : abs($this->params['cnt_limit']);

        $q = "SELECT content_id, date, title, path, short, text, photo
        FROM content
        WHERE active=1
          " . ($content_id ? "AND pid=" . $content_id : "") . "
          ORDER BY date DESC, content_id" . ($cnt_limit ? ' LIMIT ' . $cnt_limit : '');
        $rows = SFDB::assoc($q);

        if (count($rows)) {
            if (!empty($this->params['tpl']) && is_file(dirname(__FILE__) . '/tpl/' . $this->params['tpl'] . '.tpl')) {
                include dirname(__FILE__) . '/tpl/' . $this->params['tpl'] . '.tpl';
            } else {
                include dirname(__FILE__) . '/tpl/mod_list.tpl';
            }
        }
    }

}
