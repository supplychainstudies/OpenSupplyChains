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

class Controller_Edit extends Sourcemap_Controller_Map {
    public $template = 'edit';

    public function action_index($supplychain_id=false) {
        if(!$supplychain_id) $this->request->redirect('home');
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                $supplychain = $supplychain->kitchen_sink($supplychain->id);

                // Load form template
                $form = Sourcemap_Form::load('/edit');
                $form->action('edit/'.$supplychain->id)->method('post');

                // Populate fields
                $form->field('title')
                    ->add_class('required');
                if(isset($supplychain->attributes->title))
                    $form->field('title')->value($supplychain->attributes->title);

                if(isset($supplychain->attributes->description))
                    $form->field('description')->value($supplychain->attributes->description);

                $form->field('tags')
                    ->add_class('tags');
                if(isset($supplychain->attributes->tags))
                    $form->field('tags')->value($supplychain->attributes->tags);

                // fetch the taxonomy tree and use first level
                $taxonomy = Sourcemap_Taxonomy::load_tree();
    
                $form->field('category')->value($supplychain->category);
                
                if(isset($supplychain->attributes->passcode))
                    $form->field('passcode')->value($supplychain->attributes->passcode);

                $form->field('publish')->value($supplychain->other_perms & Sourcemap::READ);

                if(strtolower(Request::$method) === 'post') {
                    if($form->validate($_POST)) {
                        $title = $form->get_field('title')->value();
                        $description = $form->get_field('description')->value();
                        $tags = Sourcemap_Tags::join(Sourcemap_Tags::parse($form->get_field('tags')->value()));
                        $category = $form->get_field('category')->value();
                        if($category) $supplychain->category = $category;
                        else $category = null;
                        $public = isset($_POST['publish']) ? Sourcemap::READ : 0;
                        $supplychain->attributes->title = $title;
                        $supplychain->attributes->description = $description;
                        $supplychain->attributes->tags = $tags;
                        if($form->get_field('passcode'))
                            $supplychain->attributes->passcode =  $form->get_field('passcode')->value();
                        if($public)
                            $supplychain->other_perms |= $public;
                        else
                            $supplychain->other_perms &= ~Sourcemap::READ;
                        try {
                            ORM::factory('supplychain')->save_raw_supplychain($supplychain, $supplychain->id);
                            Message::instance()->set('Map updated.', Message::SUCCESS);
                            return $this->request->redirect('view/'.$supplychain->id);
                        } catch(Exception $e) {
                            $this->request->status = 500;
                            Message::instance()->set('Couldn\t update your supplychain. Please contact support.');
                        }
                    } else {
                        Message::instance()->set('Please correct the errors below.');
                    }
                }

                $this->template->supplychain = $supplychain;
                $this->template->form = $form;
            } else {
                Message::instance()->set('You\'re not allowed to edit that map.');
                $this->request->redirect('home');
            }
        } else {
            Message::instance()->set('That map does not exist.');
            $this->request->redirect('home');
        }
    }

    public function action_visibility($supplychain_id=false) {
        $set_to = null;
        if($supplychain_id && (Request::$method === 'POST')) {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$supplychain->user_id;
                if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                    $p = Validate::factory($_POST);
                    $p->rule('publish', 'regex', array('/(yes|no)/i'))
                        ->rule('publish', 'not_empty');
                    if($p->check()) {
                        $set_to = strtolower($p['publish']) == 'yes';
                    } else {
                        Message::instance()->set('Missing required "publish" parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map doesn\'t exist.');
                $this->request->redirect('/home');
            }
        } elseif(Request::$method === 'GET') {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$sc->user_id;
                if($current_user_id && $sc->user_can($current_user_id, Sourcemap::WRITE)) {
                    $g = Validate::factory($_GET);
                    $g->rule('publish', 'regex', array('/(yes|no)/i'))
                        ->rule('publish', 'not_empty');
                    if($g->check()) {
                        $set_to = strtolower($g['publish']) == 'yes';
                    } else {
                        Message::instance()->set('Missing required "publish" parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map does not exist.');
                $this->request->redirect('/home');
            }
        } else {
            Message::instance()->set('Bad request.');
            $this->request->redirect('/home');
        }
        if($set_to !== null) {
            if($set_to === true)
                $sc->other_perms |= $set_to;
            else
                $sc->other_perms &= ~Sourcemap::READ;
            try {
                $sc->save();
            
                $scid = $supplychain_id;
                $evt = Sourcemap_User_Event::UPDATEDSC;
                try {
                    Sourcemap_User_Event::factory($evt, $sc->user_id, $scid)->trigger();
                } catch(Exception $e) {            
                }
                Cache::instance()->delete('supplychain-'.$scid);
                if(Sourcemap_Search_Index::should_index($scid)) {
                Sourcemap_Search_Index::update($scid);
                } else {
                    Sourcemap_Search_Index::delete($scid);
                }
                $szs = Sourcemap_Map_Static::$image_sizes;
                foreach($szs as $snm => $sz) {
                    $ckey = Sourcemap_Map_Static::cache_key($scid, $snm);
                    Cache::instance()->delete($ckey);
                }
                
                Message::instance()->set('Map updated.', Message::SUCCESS);
                return $this->request->redirect('/home');
            } catch(Exception $e) {
                $this->request->status = 500;
                Message::instance()->set('Couldn\t update your supplychain. Please contact support.');
            }
        }
    }

    // For user_featured
    public function action_featured($supplychain_id=false) {
        $set_to = false;
        if($supplychain_id && (Request::$method === 'POST')) {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$supplychain->user_id;
                if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                    $p = Validate::factory($_POST);
                    $p->rule('featured', 'regex', array('/(yes|no)/i'))
                        ->rule('featured', 'not_empty');
                    if($p->check()) {
                        $set_to = strtolower($p['featured']) == 'yes';
                    } else {
                        Message::instance()->set('Missing required "featured" parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map doesn\'t exist.');
                $this->request->redirect('/home');
            }
        } elseif(Request::$method === 'GET') {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$sc->user_id;
                if($current_user_id && $sc->user_can($current_user_id, Sourcemap::WRITE)) {
                    $g = Validate::factory($_GET);
                    $g->rule('featured', 'regex', array('/(yes|no)/i'))
                        ->rule('featured', 'not_empty');
                    if($g->check()) {
                        $set_to = strtolower($g['featured']) == 'yes';
                    } else {
                        Message::instance()->set('Missing required "featured" parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map does not exist.');
                $this->request->redirect('/home');
            }
        } else {
            Message::instance()->set('Bad request.');
            $this->request->redirect('/home');
        }
        if($set_to !== null) {
            if($set_to === true) {
                if($set_to)
                {    $sc->user_featured = "TRUE";}
                else
                {    $sc->user_featured = "FALSE";}
            } else {
                $sc->user_featured = "false";
            }
            try {
                $sc->save();
            
                $scid = $supplychain_id;
                $evt = Sourcemap_User_Event::UPDATEDSC;
                try {
                    Sourcemap_User_Event::factory($evt, $sc->user_id, $scid)->trigger();
                } catch(Exception $e) {            
                }
                Cache::instance()->delete('supplychain-'.$scid);
                if(Sourcemap_Search_Index::should_index($scid)) {
                Sourcemap_Search_Index::update($scid);
                } else {
                    Sourcemap_Search_Index::delete($scid);
                }
                $szs = Sourcemap_Map_Static::$image_sizes;
                foreach($szs as $snm => $sz) {
                    $ckey = Sourcemap_Map_Static::cache_key($scid, $snm);
                    Cache::instance()->delete($ckey);
                }
                
                Message::instance()->set('Map updated.', Message::SUCCESS);
                return $this->request->redirect('/home');
            } catch(Exception $e) {
                $this->request->status = 500;
                Message::instance()->set('Couldn\t update your supplychain. Please contact support.' . $e);
                return $this->request->redirect('/home');
            }
        }
    }

    public function action_general($supplychain_id=false) {
        $private_permission = false;
        $user = ORM::factory('user', Auth::instance()->get_user());
        $admin = ORM::factory('role')
            ->where('name', '=', 'admin')->find();
        $channel = ORM::factory('role')
            ->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel)||$user->has('roles', $admin)) $private_permission = true;

        $set_to = false;
        $set_publish = null;
        $set_featured = null;
        $isset_passcode = false;
        $set_passcode = null;
        
        if($supplychain_id && (Request::$method === 'POST')) {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$supplychain->user_id;
                if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                    $p = Validate::factory($_POST);
                    $p->rule('publish', 'regex', array('/(yes|no)/i'))
                      ->rule('publish', 'not_empty')
                      ->rule('featured', 'not_empty');
                    if($p->check()) {
                        $set_to = true;
                        if(isset($p['publish']))
                            $set_publish = strtolower($p['publish']) == 'yes';
                        if(isset($p['featured']))
                            $set_featured = strtolower($p['featured']) == 'yes';
                        $isset_passcode = isset($_POST['passcode']);
                        if($isset_passcode)
                            $set_passcode = $_POST['passcode'];

                    } else {
                        Message::instance()->set('Missing required parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map doesn\'t exist.');
                $this->request->redirect('/home');
            }
        } elseif(Request::$method === 'GET') {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$sc->user_id;
                if($current_user_id && $sc->user_can($current_user_id, Sourcemap::WRITE)) {
                    $supplychain = $sc->kitchen_sink($sc->id);
                    $p = Validate::factory($_GET);
                    $p->rule('publish', 'regex', array('/(yes|no)/i'))
                      ->rule('publish', 'not_empty')
                      ->rule('featured', 'not_empty');
                    if($p->check()) {
                        $set_to = true;
                        if(isset($p['publish']))
                            $set_publish = strtolower($p['publish']) == 'yes';
                        if(isset($p['featured']))
                            $set_featured = strtolower($p['featured']) == 'yes';
                        $isset_passcode = isset($_GET['passcode']);
                        if($isset_passcode)
                            $set_passcode = $_GET['passcode'];
                        
                    } else {
                        Message::instance()->set('Missing required parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map does not exist.');
                $this->request->redirect('/home');
            }
        } else {
            Message::instance()->set('Bad request.');
            $this->request->redirect('/home');
        } // End POST/GET request
        // set_to : If parameter is set
        if($set_to) {
            if($private_permission==true){
                if($set_publish === true)
                    $sc->other_perms |= $set_publish;
                else
                    $sc->other_perms &= ~Sourcemap::READ;
            } else {
                if($set_publish === true){
                    // private to public
                    $sc->other_perms |= $set_publish;
                } else {
                    // not allowed free account set to private
                    Message::instance()->set('You are not allowed to private this map. Please contact support.');
                    //echo 'You are not allowed to private this map.';
                    //$this->_rest_error(404, "aaa");
                    return $this->request->redirect('home',400);
                }
            }

            if($set_featured === true)
                $sc->user_featured = "TRUE";
            else
                $sc->user_featured = "FALSE";
            
            if($isset_passcode)
                $supplychain->attributes->passcode =  $set_passcode;
              
            // try to save it
            try {
                ORM::factory('supplychain')->save_raw_supplychain($supplychain, $supplychain->id);
                // sc cant exec first
                $sc->save();

                $scid = $supplychain_id;
                $evt = Sourcemap_User_Event::UPDATEDSC;
                try {
                    Sourcemap_User_Event::factory($evt, $sc->user_id, $scid)->trigger();
                } catch(Exception $e) {            
                }
                Cache::instance()->delete('supplychain-'.$scid);
                if(Sourcemap_Search_Index::should_index($scid)) {
                    Sourcemap_Search_Index::update($scid);
                } else {
                    Sourcemap_Search_Index::delete($scid);
                }
                $szs = Sourcemap_Map_Static::$image_sizes;
                foreach($szs as $snm => $sz) {
                    $ckey = Sourcemap_Map_Static::cache_key($scid, $snm);
                    Cache::instance()->delete($ckey);
                }
            
                Message::instance()->set('Map updated.', Message::SUCCESS);
                return $this->request->redirect('/home');
            } catch(Exception $e) {
                $this->request->status = 500;
                Message::instance()->set('Couldn\'t update your supplychain. Please contact support.');
            }
        }
    }


    protected function  _rest_error($code=400, $msg='Not found.') {
        $this->request->status = $code;
        $this->headers['Content-Type'] = 'application/json';
        $this->response = array(
            'error' => $msg
        );
    }
}
