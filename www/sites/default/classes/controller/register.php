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

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template'
        );

        $f = Sourcemap_Form::load('/register');
        $f->action('register')->method('post');

        $this->template->form = $f;

        if(strtolower(Request::$method) === 'post') { 
             $validate= $f->validate($_POST);   
             if (array_key_exists('recaptcha', Kohana::modules())) { 
                             $recap = Recaptcha::instance();  
                 $revalid = (BOOL)($recap->is_valid($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"])); 
                $validate = ($validate && $revalid);
            }    
             if( $validate ) {  
                $p = $f->values();

                if(!preg_match("/^[a-zA-Z]/",$p['username']))
                {
                    Message::instance()->set('Please use alphabetical character as first letter of your Username.');
                    return;
                }
                
                // check for username in use
                $exists = ORM::factory('user')->where('username', '=', $p['username'])->find()->loaded();
                if($exists) {
                    Message::instance()->set('That username is taken.');
                    return;
                }
                // check for email in use
                $exists = ORM::factory('user')->where('email', '=', $p['email'])->find()->loaded();
                if($exists) {
                    Message::instance()->set('An account exists for that email address.');
                    return;
                }

                $new_user = ORM::factory('user');
                $new_user->username = $p['username'];
                $new_user->email = $p['email'];
                $new_user->password = $p['password'];
                $new_user->save();

                if(!$new_user->id) {
                    Message::instance()->set('Could not complete registration. Please contact support.');
                    return $this->request->redirect('register');
                }

                //send a notification 
				$mail = new Mail;
				$mail_object = $mail->factory('smtp', array());
				$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => 'Re: Your New Sourcemap Account');
				
                $h = md5(sprintf('%s-%s', $new_user->username, $new_user->email));
                $lid = strrev(base64_encode($new_user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);
                $msgbody = "\n";
                $msgbody .= "Dear {$new_user->username},\n\n";
                $msgbody .= "Welcome to Sourcemap!\n";
                $msgbody .= " Please click the link below to active your account:\n\n";
                $msgbody .= $url."\n\n";
                $msgbody .= "If you have any questions, please email support@sourcemap.com.\n\n";
                $msgbody .= "-The Sourcemap Team\n";


                try { 
					$sent = $mail_object->send($new_user->email, $headers, $msgbody);
                    Message::instance()->set('Activation email sent.');
                    return $this->request->redirect('register/thankyou');
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete registration. Please contact support.');
                } 

                if(isset($p['sourcemaporg_account']) && $p['sourcemaporg_account']) {
                    try { 
						$subj = "MIGRATE REQUEST: ".$p['sourcemaporg_account']; 
	 					$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => $subj);
						$msgbody = "\n";
                        $msgbody .= 'New user '.$new_user->username.' requested migration from Sourcemap.org.'."\r\n\r\n";
                        $msgbody .= "Sourcemap.org Account Name: {$p['sourcemaporg_account']}\r\n";
                        $msgbody .= "New User Email: {$new_user->email}\r\n\r\n";
                        $msgbody .= "Go to: ".URL::site('user/'.$new_user->id, true)." to view this user's profile.\r\n";
                        $msgbody .= "Go to: ".URL::site('admin/users/'.$new_user->id, true)." to view this user's details.\r\n";
                        $sent = $mail_object->send(self::MIGRATE_EMAIL,$headers, $msgbody); 
                    } catch(Exception $e) {
                        error_log('COULD NOT SEND MIGRATION REQUEST EMAIL FOR: '.$new_user->username.':'.$p['sourcemaporg_account']);
                        Message::instance()->set('We had trouble contacting the Sourcemap team. Please email us at '.self::MIGRATE_EMAIL
                            .' to help us make sure things go smoothly.');
                    }
                }
                return $this->request->redirect('register');
            } else {
                Message::instance()->set('Check the information below and try again.');
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
            $user = ORM::factory('user')->where('username', '=', $username)
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
