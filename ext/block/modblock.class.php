<?php
/**
* ModBlock class
*
* Output html block in any place
*
* @author Evgeny Shilov <evgeny@internet-menu.ru>
* @version 1.0
*/


class ModBlock extends SFModBase {

  protected function content() {
    echo empty($this->params['content']) ? '' : $this->params['content'];
  }
}
