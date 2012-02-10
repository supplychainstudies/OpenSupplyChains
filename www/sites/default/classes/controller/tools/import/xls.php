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

class Controller_Tools_Import_Xls extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
    public $template = 'tools/import/xls';

    public function action_index() {
        if(!Auth::instance()->get_user())  {
            Message::instance()->set('You must be signed in to use the importer.');
            $this->request->redirect('/auth?next=/tools/import/xls');
        }
        $current_user = Auth::instance()->get_user();
        $import_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        $admin_role = ORM::factory('role')->where('name', '=', 'admin')->find();
        $this->layout->scripts = array(
            'sourcemap-core'
        );
        if(strtolower(Request::$method) === 'post') {
            $posted = (object)array_merge($_POST, Sourcemap_Upload::get_uploads());
			if(isset($posted->file)) {
				$posted->xls_file = $posted->file;
			}
            if(isset($posted->xls_file) && $posted->xls_file instanceof Sourcemap_Upload && $posted->xls_file->ok()) {

				// Prevent blank titles
                if ((!isset($posted->title) || $posted->title == "") && ($posted->replace_into == 0)){
                    Message::instance()->set('Please provide a title.');
                    $this->request->redirect('create');
                }

                $xls = $posted->xls_file->get_contents();
                try {
                    $sc = Sourcemap_Import_Xls::xls2sc($xls, $posted);
                } catch(Exception $e) {
                    die($e);
                    Message::instance()->set('Problem with import: '.$e->getMessage());
                    $this->request->redirect('tools/import/xls');
                }
                $sc->user_id = Auth::instance()->get_user()->id;
                $update = false;
                if(isset($posted->replace_into) && $posted->replace_into > 0) {
                    if(!(ORM::factory('supplychain', $posted->replace_into)->owner->id == $sc->user_id)) {
                        Message::instance()->set('That supplychain doesn\'t exist or doesn\'t belong to you.');
                        $this->request->redirect('create');
                    } else {
                        $update = (int)$posted->replace_into;
                        
                        // Grab attributes from old supplychain (this is kind of ugly, but necessary)
                        $supplychain = ORM::factory('supplychain', $update);
                        $old_sc = $supplychain->kitchen_sink($supplychain->id);
                        $sc->attributes = $old_sc->attributes;
                    }
                } else {
                    $sc->attributes->title = $posted->title;
                    $sc->attributes->description = $posted->description;
                }
                if($update == false) {
                    $new_sc_id = ORM::factory('supplychain')->save_raw_supplychain($sc);
                } else {
                    $new_sc_id = ORM::factory('supplychain')->save_raw_supplychain($sc, $update);
                }
                $new_sc = ORM::factory('supplychain', $new_sc_id);
                if(isset($posted->publish) && $posted->publish) {
                    $new_sc->other_perms |= Sourcemap::READ;
                    $new_sc->save();
                }
                if(isset($posted->supplychain_name) && is_string($posted->supplychain_name)) {
                    $attr = ORM::factory('supplychain_attribute');
                    $attr->supplychain_id = $new_sc_id;
                    $attr->key = 'title';
                    $attr->value = substr($posted->supplychain_name, 0, 64);
                    $attr->save();
                    $attr = ORM::factory('supplychain_attribute');
                    $attr->supplychain_id = $new_sc_id;
                    $attr->key = 'name';
                    $attr->value = substr($posted->supplychain_name, 0, 64);
                    $attr->save();
                }
                if($new_sc_id)
                    Request::instance()->redirect('view/'.$new_sc_id);
                else
                    return $this->_internal_server_error('Could not save.');
            } else {
                //return $this->_bad_request('Stop file required.');
            }
        } else {
            $this->template->user_supplychains = ORM::factory('supplychain')
                ->where('user_id', '=', Auth::instance()->get_user()->id)->find_all();
        }
    } 
}
