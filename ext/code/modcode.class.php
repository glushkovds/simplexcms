<?php

class ModCode extends SFModBase {

    protected function content() {
        echo empty($this->params['content']) ? '' : $this->params['content'];
    }

}
