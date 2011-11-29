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

class Controller_Home extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'home';

    public function action_index() {

        $this->layout->scripts = array(
            'sourcemap-core',
        );
        $admin = ORM::factory('role')->where('name', '=', 'admin')->find();
        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('');
        } else if(Auth::instance()->get_user() && Auth::instance()->get_user()->has('roles', $admin)) {
    		$this->request->redirect('admin/');           	
    	}

        $user_arr = $user->as_array();
        unset($user_arr['password']);

        $scs = array();
        $scs_favorite = array();
        foreach($user->supplychains->order_by('modified', 'desc')->find_all() as $i => $sc) {
            $scs[] = $sc->kitchen_sink($sc->id);
        }
        foreach($user->favorites->find_all() as $i => $sc) {
            $scs_favorite[] = $sc->kitchen_sink($sc->id);
        }

        $isChannel = false;
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role))
            $isChannel = true;

        // Profile pictures url
        //$user_arr['avatar'] = Gravatar::avatar($user->email, 128);
        $user_arr['avatar'] = "services/uploads?bucket=accountpics&filename=".$user_arr['username'];

        $this->template->isChannel = $isChannel;
        $this->template->user = (object)$user_arr;
        $this->layout->page_title = "Dashboard for ".$this->template->user->username." on Sourcemap";
        $this->template->user_event_stream = Sourcemap_User_Event::get_user_stream($user->id, 6);
        $this->template->supplychains = $scs;
        $this->template->favorites = $scs_favorite;
    }

    public function action_update(){
        // This is an example of how we should do AJAX validation in Kohana
        $this->auto_render = FALSE;
        $this->template = null;
         
        $user = Auth::instance()->get_user();
        $set_to = null;
        if(Request::$method === 'POST') {
            if(!($user = Auth::instance()->get_user())) {
                echo "not logged in";
                $this->request->redirect('/auth');
            } else {
                // user logged in, now let's validate the content
                $p = Kohana::sanitize($_POST);
                $p = Validate::factory($p);
                $p->rule('description', 'max_length', array('10000'));
                $p->rule('url', 'url');
                $p->rule('banner_url', 'url');
                $p->rule('display_name', 'max_length', array('127'));
                if($p->check()) {
                    // update db
                    foreach ($p as $i=>$field){
                        if(!($p[$i] == "")){
                            $user->$i = $p[$i];
                        }
                    }
                    $user->save();
                    echo "success";
                } else {
                    echo "failure";
                }
            }
        }
        else{
            echo "none";
        }
    }
}
