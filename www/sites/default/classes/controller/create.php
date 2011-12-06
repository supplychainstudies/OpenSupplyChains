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

class Controller_Create extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'create';
    
    public function action_index() {
    	$this->layout->page_title = 'Create a Sourcemap';
    	
        $f = Sourcemap_Form::load('/create');
        $f->action('create')->method('post');

        if(!Auth::instance()->get_user()) {
            $this->request->redirect('auth');
        }
        
        $this->layout->scripts = array(
            'sourcemap-template'
        );

        $import_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        $admin_role = ORM::factory('role')->where('name', '=', 'admin')->find();
    	if(Auth::instance()->get_user()->has('roles', $import_role) || Auth::instance()->get_user()->has('roles', $admin_role)) {
    		$this->template->can_import = true;                         	
    	} else { 
            $this->template->can_import = false; 
        }
        $this->template->create_form = $f;
    

        if(strtolower(Request::$method) === 'post') {
            if($f->validate($_POST)) {
                // create!
                $p = $f->values();
                $title = $p['title'];
                $description = substr($p['description'], 0, 80);
                $tags = Sourcemap_Tags::join(Sourcemap_Tags::parse($p['tags']));
                $category = $p['category'];
                $public = isset($_POST['publish']) ? Sourcemap::READ : 0;
                $raw_sc = new stdClass();
                if($category) $raw_sc->category = $category;
                $raw_sc->attributes = new stdClass();
                $raw_sc->attributes->title = $title;
                $raw_sc->attributes->description = $description;
                $raw_sc->attributes->tags = $tags;
                $raw_sc->stops = array();
                $raw_sc->hops = array();
                $raw_sc->user_id = Auth::instance()->get_user()->id;
                $raw_sc->other_perms = 0;
                $raw_sc->user_featured = false;
                if($public)
                    $raw_sc->other_perms |= $public;
                else
                    $raw_sc->other_perms &= ~Sourcemap::READ;
                try {
                    $new_scid = ORM::factory('supplychain')->save_raw_supplychain($raw_sc);
                    return $this->request->redirect('view/'.$new_scid);
                } catch(Exception $e) {
                    $this->request->status = 500;
                    Message::instance()->set('Couldn\t create your supplychain. Please contact support.');
                }
            } else {
                Message::instance()->set('Correct the errors below.');
            }
        }
    }
}
