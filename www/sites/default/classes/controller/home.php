<?php
class Controller_Home extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'home';

    public function action_index() {
        $this->layout->scripts = array(
            'sourcemap-core',
        );
        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('');
        }
        if($user->profile); // load profile...
        $user_arr = $user->as_array();
        unset($user_arr['password']);
        $this->template->user = $user_arr;
        $this->template->user_event_stream = Sourcemap_User_Event::get_user_stream($user->id, 20);
    }
}
