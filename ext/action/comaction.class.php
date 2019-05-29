<?php

/**
 * Используйте для построения полноценной модели MVC.<br/>
 * Наследуйте от нижеизложенного класса свои компоненты и не будет необходимости описывать каждый action (метод) в методе content.<br/>
 * @example http://site.ru/myext/myact/ вызовет метод myact из компонента myext<br/>
 *  
 */
class ComAction extends SFComBase {

    protected $s; // private session for class
    protected $d; // private data for class
    protected $action = '';

    public function __construct() {
        $this->s = & $_SESSION[get_class($this)]['session'];
        if (!isset($this->d)) {
            $this->d = array();
        }
    }

    protected function content() {
        $mname = $this->mname();
        if (!method_exists($this, $mname)) {
            SFCore::error404();
        }
        $this->$mname(...array_slice(SFCore::uri(), 2));
    }

    /**
     * Ищет пользовательский action для выполнения
     * @return string
     */
    protected function mname() {
        if ($this->action) {
            return $this->action;
        }

        // по умолчанию
        $mname = $default = "index";
        $uri = SFCore::uri();
        // если /article/1/
        if (isset($uri[1]) && (int) $uri[1]) {
            $mname = 'item';
        }
        // читаем URI
        elseif (isset($uri[1]) && $uri[1]) {
            $mname = $uri[1];
        }

        return 'action' . ucfirst($mname);
    }

    public function index() {
        
    }

}
