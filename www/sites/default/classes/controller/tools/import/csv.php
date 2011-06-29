<?php
class Controller_Tools_Import_Csv extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
    public $template = 'tools/import/csv';

    public function action_index() {
        if(!Auth::instance()->get_user()) 
            return $this->_forbidden('You must be logged in to use the import tool.');
        $current_user = Auth::instance()->get_user();
        $import_role = ORM::factory('role')->where('name', '=', 'import')->find();
        $admin_role = ORM::factory('role')->where('name', '=', 'admin')->find();
        if($current_user->has('roles', $import_role) || $current_user->has('roles', $admin_role)) {
            // pass
        } else {
            return $this->_forbidden('You don\'t have access to that part of the site.');
        }
        $this->layout->scripts = array(
            'sourcemap-core'
        );
        if(strtolower(Request::$method) === 'post') {
            $posted = (object)array_merge($_POST, Sourcemap_Upload::get_uploads());
            if(isset($posted->stop_file) && $posted->stop_file instanceof Sourcemap_Upload && $posted->stop_file->ok()) {
                $stop_csv = $posted->stop_file->get_contents();
                if(isset($posted->hop_file) && $posted->hop_file instanceof Sourcemap_Upload && $posted->hop_file->ok())
                    $hop_csv = $posted->hop_file->get_contents();
                else
                    $hop_csv = null;
                try {
                    $sc = Sourcemap_Import_Csv::csv2sc($stop_csv, $hop_csv, $posted);
                } catch(Exception $e) {
                    die($e);
                    Message::instance()->set('Problem with import: '.$e->getMessage());
                    $this->request->redirect('tools/import/csv');
                }
                $sc->user_id = Auth::instance()->get_user()->id;
                $update = false;
                if(isset($posted->replace_into) && $posted->replace_into > 0) {
                    if(!(ORM::factory('supplychain', $posted->replace_into)->owner->id == $sc->user_id)) {
                        Message::instance()->set('That supplychain doesn\'t exist or doesn\'t belong to you.');
                        $this->request->redirect('tools/import/csv');
                    } else {
                        $update = (int)$posted->replace_into;
                    }
                }
                if($update) {
                    $new_sc_id = ORM::factory('supplychain')->save_raw_supplychain($sc, $update);
                } else {
                    $new_sc_id = ORM::factory('supplychain')->save_raw_supplychain($sc);
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
                    Request::instance()->redirect('map/view/'.$new_sc_id);
                else
                    return $this->_internal_server_error('Could not save.');
            } else {
                return $this->_bad_request('Stop file required.');
            }
        } else {
            $this->template->user_supplychains = ORM::factory('supplychain')
                ->where('user_id', '=', Auth::instance()->get_user()->id)->find_all();
        }
    } 
}
