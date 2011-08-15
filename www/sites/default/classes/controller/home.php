<?php
class Controller_Home extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'home';

    public function action_index() {
        $this->layout->scripts = array(
            'sourcemap-core',
        );
    	$admin = ORM::factory('role')->where('name', '=', 'admin')->find();
        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('');
        } else if(Auth::instance()->get_user() && Auth::instance()->get_user()->has('roles', $admin)) {
			$this->request->redirect('admin/');           	
		}

        $p = false;

        $user_arr = $user->as_array();
        unset($user_arr['password']);

        $scs = array();
        // todo: group ownership?
        foreach($user->supplychains->order_by('modified', 'desc')->find_all() as $i => $sc) {
            $scs[] = $sc->kitchen_sink($sc->id);
        }

        $this->template->user = (object)$user_arr;
        $this->layout->page_title = "Dashboard for ".$this->template->user->username." on Sourcemap";
        $this->template->user_event_stream = Sourcemap_User_Event::get_user_stream($user->id, 6);
        $this->template->user_profile = $p;
        $this->template->supplychains = $scs;
    }
}
