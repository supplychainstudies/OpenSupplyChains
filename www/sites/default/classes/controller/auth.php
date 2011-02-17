<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Auth extends Sourcemap_Controller_Layout {
    
    public $layout = 'layout';
    public $template = 'auth';

    public function action_index() {
        $this->layout->page_title = Auth::instance()->get_user() ? 'Logged in.' : 'Log in';
        if(Auth::instance()->get_user()) {
            $this->template->current_user_id = Auth::instance()->get_user();
            $this->template->current_user = ORM::factory('user', Auth::instance()->get_user());
        } else {
            $this->template->current_user_id = false;
            $this->template->current_user = false;
        }
    }

    public function action_login() {
        $post = Validate::factory($_POST);
        $post->rule('username', 'not_empty')
            ->rule('username', 'max_length', array(318))
            ->rule('username', 'min_length', array(4))
            ->rule('password', 'not_empty')
            ->rule('password', 'max_length', array(16))
            ->rule('password', 'min_length', array(6))
            ->filter(true, 'trim');
        if($post->check()) {
            $post = (object)$post->as_array();
            if(Auth::instance()->login($post->username, $post->password)) {
                Message::instance()->set('Welcome.', Message::SUCCESS);
            } else {
                Message::instance()->set('Invalid username/password combo.', Message::ERROR);
            }
        } else {
            Message::instance()->set('Invalid username/password combo.', Message::ERROR);
        }
        $this->request->redirect('auth/');
    }

    public function action_logout() {
        $auth = Auth::instance();
        if($auth->logged_in()) {
            $auth->logout(true);
        }
        $this->request->redirect('');
    }
}
