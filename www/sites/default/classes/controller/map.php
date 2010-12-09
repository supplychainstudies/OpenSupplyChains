<?php
class Controller_Map extends Sourcemap_Controller_Layout {
    
    public $layout = 'map';
    public $template = 'map/view';
    
    public function action_index() {}

    public function action_view($supplychain_id) {
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            if($supplychain->other_perms & Sourcemap::READ) {
                $this->template->supplychain_id = $supplychain->id;
            } else {
                $this->request->status = 403;
                $this->layout = View::factory('layout/error');
                $this->template = View::factory('error');
                $this->template->error_message = 'This map is private.';
            }
        } else {
            $this->request->status = 404;
            $this->layout = View::factory('layout/error');
            $this->template = View::factory('error');
            $this->template->error_message = 'That map could not be found.';
        }
    }
}
