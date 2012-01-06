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

class Sourcemap_Controller_Layout extends Controller_Template {

    public $layout = 'base';
    public $template = 'template';

    public $default_styles = array(
        'sites/default/assets/styles/reset.css',
        'sites/default/assets/styles/modal.less',
        'assets/styles/general.less',
    );

    public $ssl_required = false; // controller-wide ssl required
    public $ssl_actions  = false; // action specific ssl required

    public function before() {
        if (Kohana::config('sourcemap.debug')) { $this->profiler = new Profiler; } // enable profiler for debug mode
        
        $pret = parent::before();
        $config = Kohana::config('sourcemap');
       
        if(Kohana::config('sourcemap.use_ssl')){ $this->sslRedirect(); }
        if($this->auto_render === true) {
            $this->layout = View::factory('layout/'.$this->layout);
        }
        return $pret;
    }

    private function sslRedirect(){
       // via http://www.jamie.co.za/2010/10/07/kohana-https-redirect/

       $action_name = Request::instance()->action;
     
       if (($this->ssl_required) || (is_array($this->ssl_actions) && in_array($action_name, $this->ssl_actions))){
          if (Request::$protocol == 'http'){
             Request::Instance()->redirect(URL::site(Request::Instance()->uri, 'https'));
          }
       }
       else{
          if (Request::$protocol == 'https'){
             Request::Instance()->redirect(URL::site(Request::Instance()->uri, 'http'));
          }
       }
    }

    public function after() {
        $pret = parent::after();
        if($this->auto_render === true) {
            if(!isset($this->layout->styles) || !$this->layout->styles) 
                $this->layout->styles = $this->default_styles;
            $this->layout->content = $this->request->response;
            $this->request->response = $this->layout;
        }

        return $pret;
    }

    public function _forbidden($msg='Forbidden') {
        $this->template = View::factory('error');
        $this->template->error_message = $msg;
        return;
    }
}
