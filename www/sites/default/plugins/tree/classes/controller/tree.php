<?php

class Controller_Tree extends Sourcemap_Controller_Layout {
    
    public $layout = 'embed';
    public $template = 'tree';

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
            $this->layout->scripts = array('tree-view');
            $this->layout->styles = array(
                'sites/default/assets/styles/modal.less',
                'sites/default/plugins/tree/assets/styles/tree.less'
            );

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

            // Restrict to users with either the admin or hiviz role
            if($supplychain->user_can($current_user_id, Sourcemap::READ) && ($isadmin == true || $ishviz == true)) {

                $this->layout->supplychain_id = $supplychain_id;
                $supplychain_desc = "";
                
                // Check description for shortcodes
                // Only youtube ID is supported for now...
                if (isset($sc->attributes->description)) {
                    $supplychain_desc = $sc->attributes->description;
                    $regex = "/\\[youtube:([^]]+)]/";
                    if (preg_match($regex, $supplychain_desc, $regs)) {
                        $supplychain_youtube_id = $regs[1];
                        $supplychain_desc = str_replace($regs[0], '', $supplychain_desc);
                    }
                }

                // Do we have a passcode? 
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
                isset($supplychain_youtube_id) ? $this->template->supplychain_youtube_id = $supplychain_youtube_id : "" ;

    			$this->template->supplychain_taxonomy = isset($sc->taxonomy) ? $sc->taxonomy : array();
                
                $this->template->supplychain_weight = isset($sc->attributes->{'sm:ui:weight'}) ? "checked" : "";
                $this->template->supplychain_co2e = isset($sc->attributes->{"sm:ui:co2e"}) ? "checked" : "";
                $this->template->supplychain_water = isset($sc->attributes->{"sm:ui:water"}) ? "checked" : "";
                $this->template->supplychain_tileset = isset($sc->attributes->{"sm:ui:tileset"}) ? $sc->attributes->{"sm:ui:tileset"} : "";

    			$this->layout->page_title = $this->template->supplychain_name.' on Sourcemap';
    	        
                $this->template->can_edit = (bool)$supplychain->user_can($current_user_id, Sourcemap::WRITE);

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
