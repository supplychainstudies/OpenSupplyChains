<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

class Controller_Register extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'register';

    const MIGRATE_EMAIL = 'account-migration@sourcemap.com';

    public function action_index() { 
        if(Auth::instance()->get_user()) {
            $this->template->current_user_id = Auth::instance()->get_user();
            $this->template->current_user = ORM::factory('user', Auth::instance()->get_user());
               $this->request->redirect('home/');
        }
        $this->layout->page_title = 'Register an account on Sourcemap';

        $f = Sourcemap_Form::load('/register');
        $f->action('register')->method('post');

        $this->template->form = $f;

        if(strtolower(Request::$method) === 'post') { 
            $ajax = isset($_POST["_form_ajax"]) ? 'true' : 'false';
            
            if ($ajax)
                $this->auto_render=false; // will disable template rendering

            // Pass recaptcha first
            if (array_key_exists('recaptcha', Kohana::modules())) { 
                 $recap = Recaptcha::instance();  
                 $revalid = (BOOL)($recap->is_valid($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"])); 
            }
            if( !$revalid ) {
                Message::instance()->set('Incorrect captcha.');
            } else{
                // basic validation
                if (!$f->validate($_POST)){
                    $errors = $f->errors();
                    foreach($errors as $error){
                        Message::instance()->set($error[0]);
                    }

                    echo $ajax ? Message::instance()->render() : "";
                    return; 
                }

                // advanced validation
                $p = $f->values();

                if(!preg_match("/^[a-zA-Z]/",$p['username']))
                {
                    Message::instance()->set('Please use alphabetical character as first letter of your Username.');
                    echo $ajax ? Message::instance()->render() : "";
                    return;
                }

                // check for restricted username
                $restricted = FALSE;
                $restricted_names = array(
                    "browse",
                    "view",
                    "embed",
                    "admin",
                    "auth",
                    "info",
                    "list",
                    "blog",
                    "api"
                );
                foreach ($restricted_names as $restricted_name){
                    if ($p['username'] == $restricted_name){
                        Message::instance()->set('That username is restricted.  Please try a different username.');
                        echo $ajax ? Message::instance()->render() : "";
                        $restricted = TRUE;
                        break;
                    }
                }

                if ($restricted)
                    return;

                // check for username in use
                $exists = ORM::factory('user')->where('username', 'ILIKE', $p['username'])->find()->loaded();
                if($exists) {
                    Message::instance()->set('That username is taken.');
                    echo $ajax ? Message::instance()->render() : "";
                    return;
                }
                // check for email in use
                $exists = ORM::factory('user')->where('email', '=', $p['email'])->find()->loaded();
                if($exists) {
                    Message::instance()->set('An account exists for that email address.');
                    echo $ajax ? Message::instance()->render() : "";
                    return;
                }

                $new_user = ORM::factory('user');
                $new_user->username = $p['username'];
                $new_user->email = $p['email'];
                $new_user->password = $p['password'];
				$new_user->save();

                /*
				if (isset($p['email_subscribe']) == true) {
					$emailsubscriber_role = ORM::factory('role', array('name' => 'emailsubscriber'));
					$new_user->add('roles', $emailsubscriber_role)->save();					
				}
                */

                if(!$new_user->id) {
                    Message::instance()->set('Could not complete registration. Please contact support.');
                    echo $ajax ? Message::instance()->render() : "";
                    return $this->request->redirect('register');
                }

                //send a notification 
				$mailer = Email::connect(); 
				$swift_msg = Swift_Message::newInstance();

				$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => 'Re: Your New Sourcemap Account');
				
                $h = md5(sprintf('%s-%s', $new_user->username, $new_user->email));
                $lid = strrev(base64_encode($new_user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);
                $msgbody = "\n";
                $msgbody .= "Dear {$new_user->username},\n\n";
                $msgbody .= "Welcome to Sourcemap!";
                $msgbody .= " Click the link below to activate your account:\n\n";
                $msgbody .= $url."\n\n";
                $msgbody .= "If you have any questions, please email support@sourcemap.com.\n\n";
                $msgbody .= "-The Sourcemap Team\n";
                $swift_msg->setSubject('Re: Your New Sourcemap Account')
						  ->setFrom(array('noreply@sourcemap.com' => 'The Sourcemap Team'))
						  ->setTo(array($new_user->email => ''))
						  ->setBody($msgbody);
					

                try { 
					$sent = $mailer->send($swift_msg);
                    Message::instance()->set('Activation email sent.', Message::SUCCESS);
                    if ($ajax){
                        echo Message::instance()->get() ? Message::instance()->render() : false;
                        return;
                    }
                    else{
                        return $this->request->redirect('register/thankyou');
                    }
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete registration. Please contact support.');
                } 

            }
            if ($ajax){
                echo Message::instance()->get() ? Message::instance()->render() : false;
            }

        } else { 
        /* pass */ 
        }
    }

    public function action_thankyou(){
        $this->template->set_filename('register/thankyou');
    }

    public function action_confirm(){
        if(Auth::instance()->get_user()) {
            Message::instance()->set(
                'You\'re already signed in. Sign out and click the '.
                'confirmation url again.', Message::INFO
            );
            return $this->request->redirect('home');
        }
        $get = Validate::factory($_GET);
        $get->rule('t', 'regex', array('/^[A-Za-z0-9\+\/=]+-[A-Fa-f0-9]{32}$/'));
        if($get->check()) {
            list($uh, $h) = explode('-', $get['t']);
            // check token
            $username = base64_decode(strrev($uh));
            $user = ORM::factory('user')->where('username', 'ILIKE', $username)
                ->find();
            $login = ORM::factory('role')->where('name', '=', 'login')
                ->find();
            if($user->loaded()) {
                // see if acct is already confirmed
                if($user->has('roles', $login)) {
                    Message::instance()->set('That token has expired.');
                    return $this->request->redirect('auth');
                }
            } else {
                Message::instance()->set('Invalid confirmation token.');
                return $this->request->redirect('auth');
            }
            // add login role
            $user->add('roles', $login);
            Message::instance()->set('Your account has been confirmed. Please Sign in (and start mapping).', Message::SUCCESS);
            Sourcemap_User_Event::factory(Sourcemap_User_Event::REGISTERED, $user->id)->trigger();
            return $this->request->redirect('auth');
        } else {
            Message::instance()->set('Invalid confirmation token.');
            return $this->request->redirect('auth');
        }
    }
}
