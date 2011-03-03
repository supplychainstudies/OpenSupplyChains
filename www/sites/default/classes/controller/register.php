<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Register extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'register';

    public function action_index() {
	
	$this->layout->scripts = array(
	    'sourcemap-core', 'sourcemap-template', 'sourcemap-working'
	    );
	$this->layout->styles = array(
	    'assets/styles/style.css', 
	    'assets/styles/sourcemap.less?v=2'
	    );
	
	$post = Validate::factory($_POST);
	$post->rule('username', 'not_empty')
	    ->rule('username', 'max_length', array(318))
            ->rule('username', 'min_length', array(4))
	    ->rule('email', 'not_empty')
	    ->rule('email', 'max_length', array(318))
            ->rule('email', 'min_length', array(4))
	    ->rule('password', 'not_empty')
	    ->rule('password', 'max_length', array(16))
            ->rule('password', 'min_length', array(6))
	    ->rule('confirmpassword', 'not_empty')
	    ->rule('confirmpassword', 'max_length', array(16))
            ->rule('confirmpassword', 'min_length', array(6))
	    ->filter(true, 'trim');

	
	if(strtolower(Request::$method) === 'post'){
	    $post = (object)$post->as_array();
	    
	    if($post->password == $post->confirm_password) {
		$create = ORM::factory('user');
		$all_users = $create->find_all()->as_array(null, 'username');
		$all_emails = $create->find_all()->as_array(null, 'email');
		if(!in_array($post->username, $all_users)){
		    if(!in_array($post->email, $all_emails)) {
			$create->username = $post->username;                
			$create->email = $post->email;
			$create->password = $post->password;
			$create->save();       
			$this->email_user($post->username, $post->email);

		    }  else {
			Message::instance()->set('Email already exists.');
		    }
		} else {
		    Message::instance()->set('Username already exists, please try with a different username.');
		}
		
	    } else {
		Message::instance()->set('Passwords did not match.');
	    }
	    
	}
    }
    
    
    public function email_user($username, $email) {	
	
	$email_vars = array('username' => $username);
	$to = $email;
	$subject = 'Email confirmation for Sourcemap account';
	$body = View::factory('email/confirm_template')->bind('email_vars', $email_vars);
 	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sourcemap Team <smita@sourcemap.org>' . "\r\n";
	
	$mail_sent = mail($to, $subject, $body, $headers);
	
	echo $mail_sent ? "Mail sent" : "Mail failed"; 
	
    }    
  
}

