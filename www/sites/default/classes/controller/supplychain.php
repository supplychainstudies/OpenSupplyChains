<?php
class Controller_Supplychain extends Sourcemap_Controller_Layout {
    public $layout = 'supplychain';
    public $template = null;

    public function action_index() {}

    public function action_edit($id) {
        $sc = ORM::factory('supplychain', $id);
        if($sc->loaded()) {
            $user_id = Auth::instance()->logged_in();
            if($sc->user_can($user_id, Sourcemap::WRITE)) {
                $this->template = View::factory('supplychain/edit');
                $this->template->supplychain_id = $id;
            } else {
                $this->request->status = 403;
                $this->layout = View::factory('layout/error');
                $this->template = View::factory('error');
                $this->template->error_message = 'You can\'t edit that map.';
            }
        } else {
            $this->request->status = 404;
            $this->layout = View::factory('layout/error');
            $this->template = View::factory('error');
            $this->template->error_message = 'That supplychain could not be found.';
        }
    }
}
