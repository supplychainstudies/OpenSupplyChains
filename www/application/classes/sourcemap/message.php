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

/* The Sourcemap_Message class aims to provide a generic way to pass data to the user.
 *
 * When constructed normally, it works by adding messages to the session and
 * (presumably) rendering them on next page load.  
 *
 * When constructed with the ajax=true flag, it will immediately render the message and
 * return it as a string.
 *
 * This is in order to allow a controller to operate agnostically.
*/

class Sourcemap_Message {

    public $session_key = 'sourcemap_messages';
    public $_default_level = self::ERROR;
    public $_default_view = 'partial/messages';
    public $_messages_file = 'messages';
    public $_ajax_enabled = false;

    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';
    
    public function __construct($ajax=false){
        $this->_ajax_enabled = $ajax;
    }

    public function set($message, $level=null) {
        // Set message via Kohana's messages class if possible
        $message = Kohana::message('general',$message) ? Kohana::message('general',$message) : $message;
        
        $level = $level ? (string)$level : $this->_default_level;
        $messages = (array)$this->get();
        $messages[] = (object)array('message' => $message, 'level' => $level);

        // If ajax is enabled, we'll render the message immediately
        if ($this->_ajax_enabled) {
            echo self::render(true, null, $messages);
            return;
        }
        // Otherwise, add it to the session and render it on next page load
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

    public function render($clear=true, $view=null, $messages=null) {
        $view = $view === null ? $this->_default_view : $view;
        $view = View::factory($view);
        if ($messages)
            $view->set('messages', $messages);
        else
            $view->set('messages', $this->get());
        if($clear) $this->clear();
        return $view->render();
    }
}
