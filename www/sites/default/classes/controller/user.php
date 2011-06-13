<?php
class Controller_User extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'user/profile';

    public function action_index($identifier=false) {
        if(!$identifier) {
            Message::instance()->set('No user specified.');
            return $this->request->redirect('');
        }
        if(is_numeric($identifier)) {
            // pass
            $user = ORM::factory('user', $identifier);
        } else {
            $user = ORM::factory('user')->where('username', '=', $identifier)->find();
        }
        if($user->loaded()) {
            $user = (object)$user->as_array();
            unset($user->password);
            $user->avatar = Gravatar::avatar($user->email);
            unset($user->email);
            $this->template->user = $user;
            // todo: recent maps.
        } else {
            Message::instance()->set('That user doesn\'t exist.');
            return $this->request->redirect('');
        }
    }
}
