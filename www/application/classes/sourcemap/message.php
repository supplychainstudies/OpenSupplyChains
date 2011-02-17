<?php
class Sourcemap_Message {

    public $session_key = 'sourcemap_messages';
    public $_default_level = self::ERROR;
    public $_default_view = 'partial/messages';

    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    public function set($message, $level=null) {
        $level = $level ? (string)$level : $this->_default_level;
        $messages = (array)$this->get();
        $messages[] = (object)array('message' => $message, 'level' => $level);
        Session::instance()->set($this->session_key, $messages);
        return $this;
    }

    public function get() {
        return Session::instance()->get($this->session_key, array());
    }

    public function clear() {
        Session::instance()->delete($this->session_key);
        return $this;
    }

    public function render($clear=true, $view=null) {
        $view = $view === null ? $this->_default_view : $view;
        $view = View::factory($view);
        $view->set('messages', $this->get());
        if($clear) $this->clear();
        return $view->render();
    }
}
