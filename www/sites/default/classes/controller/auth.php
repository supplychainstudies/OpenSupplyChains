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
	
        $this->layout->scripts = array(
            'sourcemap-social', 'modernizr', 'less'
	    );

    }

    public function action_login() {
	$post = Validate::factory($_POST);
        $post->rule('username', 'not_empty')
            ->rule('username', 'max_length', array(318))
            ->rule('username', 'min_length', array(4))
            ->rule('password', 'not_empty')
            ->rule('password', 'max_length', array(16))
            ->rule('password', 'min_length', array(6))
	    ->rule('next', 'max_length', array(500))
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
	if (!empty($post->next)) {
	    $this->request->redirect($post->next);
	} else {
	    $this->request->redirect('auth/');
	}
    }

    public function action_logout() {
        $auth = Auth::instance();
        if($auth->logged_in()) {
            $auth->logout(true);
        }
        $this->request->redirect('');
    }


    public function action_forgot_password() {
   
	$this->template = View::factory('auth/forgot_password');
	
	$post = Validate::factory($_POST);
	$post ->rule('email', 'not_empty')
	    ->rule('email', 'validate::email')
	    ->filter(true, 'trim');
   
	if(strtolower(Request::$method) === 'post' && $post->check()){
	    $post = (object)$post->as_array();
	    $email = $post->email;
	    $user = ORM::factory('user')->where('email', '=', $email)->find();
	    $temp_password = text::random($type = 'alnum', $length = 6);
	    $user_temp = ORM::factory('user', $user->id);   
	    $user_temp->password = $temp_password;
	    $user_temp->save();
	    $this->email_password($user->username, $email, $temp_password);
	}
    }

    public function email_password($username, $email, $temp_password) {


	$email_vars = array('username' => $username, 'password' => $temp_password);
	$to = $email;
	$subject = 'Your Sourcemap account information';
	$body = View::factory('email/password_template')->bind('email_vars', $email_vars);
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sourcemap Team <info@sourcemap.org>' . "\r\n";
	
	$mail_sent = mail($to, $subject, $body, $headers);
	
	echo $mail_sent ? "Mail sent" : "Mail failed"; 
       
    }
    

    public function action_reset_password() {

	$this->template = View::factory('auth/reset_password');
	$current_user_id = Auth::instance()->get_user();
	$current_user = ORM::factory('user', Auth::instance()->get_user());
	$user_name = ORM::factory('user', $current_user_id)->username;
	$this->template->current_user_id = $current_user_id;
	$this->template->current_user = $current_user;

	//create a temp password and email that to the user.
    
	$post = Validate::factory($_POST);
	$post->rule('old', 'not_empty')
	    ->rule('new', 'not_empty')
	    ->filter(true, 'trim');
	
	if(strtolower(Request::$method) === 'post' && $post->check()) {
	    $post = (object)$post->as_array();
	    $old_password = $post->old;
	    $new_password = $post->new;

	    
	    if(Auth::instance()->check_password($old_password)) {
		$user = ORM::factory('user', $current_user_id);
		$user->password = $new_password;
		$user->save();
		Message::instance()->set('Password changed successfully!');
	    } else {
		Message::instance()->set('Password did not match, try again');
	    }
	} 
    }


   

  }
