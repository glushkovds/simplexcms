<?php

abstract class SFAdminWidgetBase extends SFExtBase {

    protected $data;
    protected $id;

    public function __construct($data) {
        $this->data = $data;
        $this->id = (int) $this->data['widget_id'];
    }

    protected abstract function content();

    protected function actions() {
        
    }

    public final function execute() {
        if (SFCore::ajax()) {
            ob_start();
            $this->content();
            return ob_get_clean();
        } else {
            ob_start();
            $this->content();
            $content = ob_get_clean();
            if ($content) {
                ob_start();
                include $_SERVER['DOCUMENT_ROOT'] . '/admin/theme/tpl/widget.layout.tpl';
                return ob_get_clean();
            }
        }
        return '';
    }

}
