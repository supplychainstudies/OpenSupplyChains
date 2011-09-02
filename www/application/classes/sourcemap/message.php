<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

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
