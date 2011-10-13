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

    public function action_index($identifier=false) {
        // TODO: cache this crap
        if(!$identifier) {
            Message::instance()->set('No user specified.');
            return $this->request->redirect('');
        }
        if(is_numeric($identifier)) {
            // pass
            $user = ORM::factory('user', $identifier);
        } else {
            $user = ORM::factory('user')->where('username', 'ILIKE', $identifier)->find();
        }        
        if($user->loaded()) {

           // Additional functions for "channel" user
           $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
           if($user->has('roles', $channel_role)) {
               $banner_url="";
               $featured = Array();

               // Load slider functionality
               $this->layout->scripts = array(
                   'sourcemap-core',
                   'sourcemap-channel'
               );
               $this->layout->styles = $this->default_styles;
               $this->layout->styles[] = 'sites/default/assets/styles/slider.less';

               // HACK: Office Depot branding info
               // This information needs to come from the database.
               if ($user->username == "officedepot"){
                   $banner_url="sites/default/assets/images/officedepot-logo.png";                   
                   $q = array(
                    'user' => $user->id,
                    'l' => 4,'recent' => 'yes'
                   );
                   $r = Sourcemap_Search::find($q);

                   $featured_ids = $r;
               }

               $this->template = new View('channel/profile');
               $this->template->banner_url = $banner_url;
               $this->template->featured = $featured_ids;
           } // channel role end           

            $user = (object)$user->as_array();
            $admin = ORM::factory('role')->where('name', '=', 'admin')->find();
            
            if(!(Auth::instance()->get_user())) {            
            unset($user->password);
            $user->avatar = Gravatar::avatar($user->email, 128);
            unset($user->email);
            $this->template->user = $user;

            $pg = isset($_GET['p']) && (int)$_GET['p'] ? $_GET['p'] : 1;
            $pg = max($pg,1);

            $l = 10;
            $q = array(
                'user' => $user->id,
                'l' => $l, 'o' => ($pg-1)*$l,
                'p' => $pg, 'recent' => 'yes'
            );

            $r = Sourcemap_Search::find($q);

            $this->template->search_result = $r;
            
            $p = Pagination::factory(array(
                'current_page' => array(
                    'source' => 'query_string',
                    'key' => 'p'
                ),
                'total_items' => $r->hits_tot,
                'items_per_page' => $r->limit,
                'view' => 'pagination/basic'
            ));

            $this->template->pager = $p;

            $this->template->supplychains = $r->results;
            }
            //  If user id match login in
            else if($user->id==Auth::instance()->get_user()->id){
                $this->request->redirect('home/');
            }
            // If user is an admin
            else if(Auth::instance()->get_user() && Auth::instance()->get_user()->has('roles', $admin)){                
            // If enter numeric user id
            if(is_numeric($identifier)) {
                 $user = ORM::factory('user', $identifier);
            } else {
                 $user = ORM::factory('user')->where('username', 'ILIKE', $identifier)->find();
            }
                        
            $p = false;            
            
            $user_arr = (object)$user->as_array();
            unset($user_arr->password);
            
            $this->template->user = $user_arr;
            $user_arr->avatar = Gravatar::avatar($user_arr->email, 128);
            $this->layout->page_title = "Dashboard for ".$this->template->user->username." on Sourcemap";
            $this->template->user_event_stream = Sourcemap_User_Event::get_user_stream($user_arr->id, 6);
            
            $scs = array();
            $scs_t =array();
            foreach($user->supplychains->order_by('modified', 'desc')->find_all() as $i => $sc) {            
                $scs[] = $sc->kitchen_sink($sc->id);
                //$scs_t[] = ($sc->id);
                //Message::instance()->set($scs_t[i]);
                
            }
             //Message::instance()->set($scs_t[0]);
             //Message::instance()->set($scs_t[1]);
             

            $this->template->user_profile = $p;
            $this->template->supplychains = $scs;



            } else {
            // User not login
            unset($user->password);
            $user->avatar = Gravatar::avatar($user->email, 128);
            unset($user->email);
            $this->template->user = $user;

            $pg = isset($_GET['p']) && (int)$_GET['p'] ? $_GET['p'] : 1;
            $pg = max($pg,1);

            $l = 10;
            $q = array(
                'user' => $user->id,
                'l' => $l, 'o' => ($pg-1)*$l,
                'p' => $pg, 'recent' => 'yes'
            );

            $r = Sourcemap_Search::find($q);

            $this->template->search_result = $r;
            
            $p = Pagination::factory(array(
                'current_page' => array(
                    'source' => 'query_string',
                    'key' => 'p'
                ),
                'total_items' => $r->hits_tot,
                'items_per_page' => $r->limit,
                'view' => 'pagination/basic'
            ));

            $this->template->pager = $p;
            $this->template->supplychains = $r->results;
            
            }
        } else {
            // User not loaded
            Message::instance()->set('That user doesn\'t exist.');
            return $this->request->redirect('');
        }
    }
}
