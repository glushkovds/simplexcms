<?php

abstract class SFModBase extends SFExtBase {

    protected $id = 0;
    protected $name = '';
    protected $title = '';
    protected $params = array();
    protected $position = '';

    public function __construct($module) {
        $this->id = (int) $module['item_id'];
        $this->name = strtolower(substr($module['class'], 3));
        $this->title = $module['name'];
        $this->position = $module['posname'];
        $this->params = unserialize($module['params']);
        $this->params['css_id'] = isset($this->params['css_id']) ? $this->params['css_id'] : 'module-' . $this->id;
        $this->params['is_title'] = isset($this->params['is_title']) ? $this->params['is_title'] : 0;
        $this->params['is_wrap'] = isset($this->params['is_wrap']) ? $this->params['is_wrap'] : 0;
    }

    protected abstract function content();

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
                if ($this->params['is_wrap']) {
                    echo '<div id="', $this->params['css_id'], '" class="module-base module-', $this->name, isset($this->params['cssclass']) ? " {$this->params['cssclass']}" : '', '">';
                    echo $this->params['is_title'] ? '<h2>' . $this->title . '</h2>' : '';
                    echo '<div class="module-body">';
                    echo $content;
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo $this->params['is_title'] ? '<h2>' . $this->title . '</h2>' : '';
                    echo $content;
                }
                return ob_get_clean();
            }
        }
        return '';
    }

}
