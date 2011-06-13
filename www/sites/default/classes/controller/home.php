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

        if($user->profile->loaded()) $p = $user->profile;
        else $p = false;

        $user_arr = $user->as_array();
        unset($user_arr['password']);

        $scs = array();
        // todo: group ownership?
        foreach($user->supplychains->order_by('modified', 'desc')->find_all() as $i => $sc) {
            $scs[] = $sc->kitchen_sink($sc->id);
        }

        $this->template->user = (object)$user_arr;
        $this->template->user_event_stream = Sourcemap_User_Event::get_user_stream($user->id, 20);
        $this->template->user_profile = $p;
        $this->template->supplychains = $scs;
    }
}
