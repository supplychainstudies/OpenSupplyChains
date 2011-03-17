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
            'sourcemap-core', 'sourcemap-template', 'sourcemap-working', 'sourcemap-social'
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
            ->rule('confirm_password', 'not_empty')
            ->rule('confirm_password', 'max_length', array(16))
            ->rule('confirm_password', 'min_length', array(6))
            ->rule('email', 'validate::email')
            ->filter(true, 'trim');
    
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            
            if($post->password == $post->confirm_password) {
                $create = ORM::factory('user');
		$create->username = $post->username;                
		$create->email = $post->email;
		$create->password = $post->password;
		try {
		    $create->save();  
		    $created = ORM::factory('user', $create->id)->created;
		    $hash_value = Auth::instance()->hash($post->username.$post->email.$created);
		    $this->email_user($post->username, $post->email, $hash_value);	    
		} catch (Exception $e){
		    Message::instance()->set('Could not register the user.');
		}
		
	    } else {
		Message::instance()->set('Passwords did not match.');
	    }
	}
    }
    
    
    public function email_user($username, $email, $hash_value) {

        $email_vars = array(
            'username' => $username,
            'hash_value' => $hash_value);

        $to = $email;
        $subject = 'Email confirmation for Sourcemap account';
        $body = View::factory('email/confirm')->bind('email_vars', $email_vars);
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: Sourcemap Team <smita@sourcemap.org>' . "\r\n";
        
	try {
	    mail($to, $subject, $body, $headers);
	} catch (Exception $e) {
	    Message::instance()->set('Sorry, could not send an email.');
	}        
    }

    public function action_confirm(){
    
	$hash = $_GET['u'];
        $users = ORM::factory('user')->find_all()->as_array('id', array('id','username', 'email', 'created'));
        foreach($users as $user) {
            if (Auth::instance()->hash($user->username.$user->email.$user->created) == $hash) {
		$user_confirm = ORM::factory('user', $user->id);
		$user_confirm->flags =2;

		try {
		    $user_confirm->save();    
		    //add a default login role when a new user is created
		    $role = ORM::factory('role', array('name' => 'login'));
		    $user_confirm->add('roles', $role)->save();
		    Message::instance()->set('Thank you, your registration is now complete.');
		} catch (Exception $e) {
		    Message::instance()->set('Could not complete the registration.');
		}
		
	    }
	}
    }

    
    public function action_loginopenid() {
	
	$errors = "";
	if(isset($_POST['token'])) {
	    $token = $_POST['token'];
	 
	    $apikey = '0cb119b5123b1731a13c0979937af366504944a4';
	    $post_data = array('token' => $_POST['token'],
			       'apiKey'=> $apikey,
			       'format' => 'json');

	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    $raw_json = curl_exec($curl);
	    curl_close($curl); 	    
	    
	    $auth_info = json_decode($raw_json, true);	    

	    if ($auth_info['stat'] == 'ok') {
		$profile = $auth_info['profile'];
		if (isset($profile['identifier'])) {
		    $identifier = $profile['identifier'];
		}
		if (isset($profile['name'])) {
		    $name = $profile['name'];
		    if (isset($name['givenName'])) {
			$username = $name['givenName'];
		    } else {
			$username = $profile['displayName'];
		    }
		}	 
		
		if (isset($profile['verifiedEmail'])) {
		    $email = $profile['verifiedEmail'];
		}
		
		$user = ORM::factory('user');
		$all_users = $user->find_all()->as_array(null, 'username');
                $all_emails = $user->find_all()->as_array(null, 'email');
		$auto_password = text::random($type = 'alnum', $length = 6);  
		
			
		if(!in_array($email, $all_emails)){
		    $username = $this->get_username($username, $all_users);
		    
		} else {
		    $get_user = $user->where('email', '=', $email)->find();
		    $username = $get_user->username;
		    
		}

	    }
	}
	$this->template->email = $email;
	$this->template->username = $username;
	$this->template->password = $auto_password;
	$this->template->identifier = $identifier;
	
    }


    public function get_username($username, $all_users){
	$count =0;
	$test = in_array($username, $all_users);
	while($test) {
	    $count++;
	    $test = in_array($username."-".$count, $all_users);
	    $username = $username."-".$count;
	}
	return $username;
    }



    
}

