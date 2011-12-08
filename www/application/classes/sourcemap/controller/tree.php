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

class Sourcemap_Controller_Tree extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
    public $template = 'tree/view';

    protected function _match_alias($alias) {
        $found = ORM::factory('supplychain_alias')
            ->where('site', '=', Kohana::config('sourcemap.site'))
            ->where('alias', '=', $alias)
            ->find_all()->as_array('alias', 'supplychain_id');
        $supplychain_id = $found ? $found[$alias] : -1;
        return $supplychain_id;
    }
    
    public function action_index($supplychain_id) {
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        $sc = $supplychain->kitchen_sink($supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {

                //redirect mobile users to mobile template
                if (Request::user_agent('mobile')){
                    $this->layout = new View('layout/mobile');
                    $this->layout->styles = array(
                        'assets/styles/mobile.less'
                    );
                    $this->template = new View('map/mobile');
                }

                $this->layout->supplychain_id = $supplychain_id;
               
                $supplychain_desc = "";
                
                // check description for shortcodes
                // only youtube ID is supported for now...
                if (isset($sc->attributes->description)) {
                    $supplychain_desc = $sc->attributes->description;
                    $regex = "/\\[youtube:([^]]+)]/";
                    if (preg_match($regex, $supplychain_desc, $regs)) {
                        $supplychain_youtube_id = $regs[1];
                        $supplychain_desc = str_replace($regs[0], '', $supplychain_desc);
                    }

                }
                //passcode for the map          
                $this->template->exist_passcode = isset($sc->attributes->passcode);

                // pass supplychain metadeta to template 
                $this->template->supplychain_id = $supplychain_id;
                $this->template->supplychain_date = date('F j, Y', $sc->created );
                $this->template->supplychain_name = isset($sc->attributes->title) ? $sc->attributes->title : (isset($sc->attributes->name) ? $sc->attributes->name : "");
                $this->template->supplychain_owner = isset($sc->owner->name) ? $sc->owner->name : "";
                isset($sc->owner->display_name) ? $this->template->supplychain_display_name = $sc->owner->display_name : "";
                $this->template->supplychain_banner_url = isset($sc->owner->banner_url) ? $sc->owner->banner_url : "";
                $this->template->supplychain_ownerid = isset($sc->owner->id) ? $sc->owner->id : "";
                $this->template->supplychain_avatar = isset($sc->owner->avatar) ? $sc->owner->avatar : "";
                $this->template->supplychain_desc = isset($supplychain_desc) ? $supplychain_desc : "" ;
                //$this->template->supplychain_youtube_id = isset($supplychain_youtube_id) ? $supplychain_youtube_id : "" ;
                isset($supplychain_youtube_id) ? $this->template->supplychain_youtube_id = $supplychain_youtube_id : "" ;

    			$this->template->supplychain_taxonomy = isset($sc->taxonomy) ? $sc->taxonomy : array();
                
                $this->template->supplychain_weight = isset($sc->attributes->{'sm:ui:weight'}) ? "checked" : "";
                $this->template->supplychain_co2e = isset($sc->attributes->{"sm:ui:co2e"}) ? "checked" : "";
                $this->template->supplychain_water = isset($sc->attributes->{"sm:ui:water"}) ? "checked" : "";
                $this->template->supplychain_tileset = isset($sc->attributes->{"sm:ui:tileset"}) ? $sc->attributes->{"sm:ui:tileset"} : "";

    			$this->layout->page_title = $this->template->supplychain_name.' on Sourcemap';
    	        
                $this->template->can_edit = (bool)$supplychain->user_can($current_user_id, Sourcemap::WRITE);
                    
                $this->layout->scripts = array('tree-view');
                $this->layout->styles = array(
                    'sites/default/assets/styles/reset.css', 
                    'assets/styles/base.less',
                    'assets/styles/general.less',
                    'sites/default/assets/styles/modal.less'                    
                );
                // comments
                $c = $supplychain->comments->find_all();
                $comment_data = array();
                foreach($c as $i => $comment) {
                    $arr = $comment->as_array();
                    $arr['username'] = $comment->user->username;
                    $arr['avatar'] = Gravatar::avatar($comment->user->email, 32);
                    $comment_data[] = (object)$arr;
                }
                $this->template->comments = $comment_data;
                $this->template->can_comment = (bool)$current_user_id;
                // qrcode url
    			$shortener = new Sourcemap_Bitly;
    			$shortlink = $shortener->shorten(URL::site('view/'.$supplychain->id, true));
                $qrcode_query = URL::query(array('q' => $shortlink, 'sz' => 3));
                $scaled_qrcode_query = URL::query(array('q' => $shortlink, 'sz' => 16));

    			$this->template->short_link = $shortlink;
                $this->template->qrcode_url = URL::site('services/qrencode', true).$qrcode_query;
                $this->template->scaled_qrcode_url = URL::site('services/qrencode', true).$scaled_qrcode_query;

            } else {
                Message::instance()->set('That supply chain is private.');
                $this->request->redirect('browse');
            }
        } else {
            Message::instance()->set('That supply chain could not be found.');
            $this->request->redirect('browse');
        }
    }
}
