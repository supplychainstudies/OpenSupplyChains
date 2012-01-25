<?php

class Controller_Tree extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
    public $template = 'view';

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
			$current_user = Auth::instance()->get_user();
			$hviz_role = ORM::factory('role')->where('name', '=', 'hviz')->find();
	        $admin_role = ORM::factory('role')->where('name', '=', 'admin')->find();
	 		$isadmin = false;
			$ishviz = false;
			if ($current_user_id != 0) {
	 			$isadmin = $current_user->has('roles', $admin_role);
				$ishviz = $current_user->has('roles', $hviz_role);
			}
            // Only user who create map / admin / hviz can read tree
            if($supplychain->user_can($current_user_id, Sourcemap::READ) && ($isadmin == true || $ishviz == true)) {
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
                    $arr['avatar'] = "services/uploads?bucket=accountpics&filename=".$comment->user->username;
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
