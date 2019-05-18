<?php

abstract class SFComBase extends SFExtBase {

    /**
     * Путь до компонента в строке браузера
     * @var string
     */
    protected $rootHref;

    public function __construct() {
        parent::__construct();
        $this->rootHref = str_replace(['\\', $_SERVER['DOCUMENT_ROOT']], ['/', ''], $this->rootDir);
    }

    protected abstract function content();

    public final function execute($is_print = false) {
        if ($is_print) {
            $this->content();
        } else {
            ob_start();
            $this->content();
            return ob_get_clean();
        }
    }

}
