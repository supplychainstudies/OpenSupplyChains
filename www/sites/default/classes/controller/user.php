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

class Controller_User extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'user/profile';

    // TODO: cache this crap
    public function action_index($identifier=false) {
        if(!$identifier) {
            Message::instance()->set('No user specified.');
            return $this->request->redirect('');
        }
        
        if(is_numeric($identifier)) {
            $user = ORM::factory('user', $identifier);
        } else {
            $user = ORM::factory('user')->where('username', 'ILIKE', $identifier)->find();
        }

        if($user->loaded()) {
            $this->layout->page_title = "Dashboard for " . $user->username . " on Sourcemap";
            $user_arr = $user->as_array();
            unset($user_arr['password']);
            
            // Redirect logged-in users to dashboard, unless preview flag is set
            $admin = ORM::factory('role')->where('name', '=', 'admin')->find();
            $preview_mode = isset($_GET["preview"]);
            $current_user_id = isset(Auth::instance()->get_user()->id) ? Auth::instance()->get_user()->id : "";
            if($user->id == $current_user_id && !($preview_mode)){
                $this->request->redirect('home/');
            }
            
            // Additional functions for "channel" user
            $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
            if($user->has('roles', $channel_role)) {
                $banner_url="";
                
                // Return all user_favorited supplychain IDs
                // TODO: turn this into a search (for better caching)
                $featured_scs = Array();
                $supplychains = $user->supplychains->order_by('modified', 'desc')->find_all(); 
                foreach ($supplychains as $i=>$supplychain){
                    $current = $supplychain->kitchen_sink($supplychain->id);
                    if ($current->user_featured){
                        $featured_scs[] = $supplychain;
                    }
                }

               // Load slider functionality
               $this->layout->scripts = array(
                   'sourcemap-core',
                   'sourcemap-channel'
               );

               $this->layout->styles = $this->default_styles;
               $this->layout->styles[] = 'sites/default/assets/styles/slider.less';
               $this->template = new View('channel/profile');
               $this->template->user_profile = $user;
               $this->template->featured = $featured_scs;
            } 

            // Normal user functions
            
            // Return all user's supplychain IDs
            // TODO: turn this into a search (for better caching)
            $scs = array();
            foreach($user->supplychains->order_by('modified', 'desc')->find_all() as $i => $sc) {            
                $scs[] = $sc->kitchen_sink($sc->id);
            }
            
            $this->template->user = (object)$user_arr;
            $this->template->avatar_url = Sourcemap_Image::avatar($user, 64, true);
            $this->template->supplychains = $scs;

        } else {
            Message::instance()->set('That user doesn\'t exist.');
            return $this->request->redirect('');
        }
    }
    
    public function action_upload() {
        if(strtolower(Request::$method) === 'post') {
            
        }
        else{
            return $this->request->redirect('');
        }
    }
}
