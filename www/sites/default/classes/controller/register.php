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
            // Have we requested AJAX?
            $ajax = isset($_POST["_form_ajax"]) ? 'true' : 'false';
            if ($ajax)
                $this->auto_render=false; 

            // Create message object
            $message = new Message($ajax);

            // Pass recaptcha first
            if (array_key_exists('recaptcha', Kohana::modules())) { 
                 $recap = Recaptcha::instance();  
                 $revalid = (BOOL)($recap->is_valid($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"])); 
            }
            if( !$revalid ) {
                $message->set('invalid-captcha');
            } else{
                // basic validation
                if (!$f->validate($_POST)){
                    $errors = $f->errors();
                    foreach($errors as $error){
                        $message->set($error[0]);
                    }
                    return; 
                }

                // advanced validation
                $p = $f->values();

                if(!preg_match("/^[a-zA-Z]/",$p['username']))
                {
                    $message->set('register-alpha-character');
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
                        $message->set('register-restricted');
                        $restricted = TRUE;
                        break;
                    }
                }

                if ($restricted)
                    return;

                // check for username in use
                $exists = ORM::factory('user')->where('username', 'ILIKE', $p['username'])->find()->loaded();
                if($exists) {
                    $message->set('register-taken');
                    return;
                }
                // check for email in use
                $exists = ORM::factory('user')->where('email', '=', $p['email'])->find()->loaded();
                if($exists) {
                    $message->set('register-email-exists');
                    return;
                }

                // Save user
                $new_user = ORM::factory('user');
                $new_user->username = $p['username'];
                $new_user->email = $p['email'];
                $new_user->password = $p['password'];
				$new_user->save();

                if(!$new_user->id) {
                    $message->set('register-generic');
                    return $this->request->redirect('register');
                }
				
                // Build outgoing message
                $h = md5(sprintf('%s-%s', $new_user->username, $new_user->email));
                $lid = strrev(base64_encode($new_user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);

                $mailer   = Email::connect();
                $from     = Kohana::message('general', 'email-from');
                $subject  = Kohana::message('general', 'register-email-subject'); 
                $msgbody  = __(Kohana::message('general', 'register-email-body'), array(
                    ':user' => $new_user->username, 
                    ':url' => $url)
                ); 
                $swift_msg = Swift_Message::newInstance();
                $swift_msg->setSubject($subject)
                          ->setFrom($from)
                          ->setTo(array($new_user->email => ''))
                          ->setBody($msgbody);

                // Send a notification 
                try { 
					$sent = $mailer->send($swift_msg);
                } catch (Exception $e) {
                    $message->set('register-generic');
                    return;
                } 
                Message::instance()->set('register-email-sent', Message::SUCCESS);
                if ($ajax){
                    echo "redirect register/thankyou"; 
                    return;
                }
                else{
                    return $this->request->redirect('register/thankyou');
                }

            }

        } else { 
        /* pass */ 
        }
    }

    public function action_thankyou(){
        $this->template->set_filename('register/thankyou');
    }

    public function action_confirm(){
        // Create message object
        $message = new Message;
        
        if(Auth::instance()->get_user()) {
            $message->set('register-already-in');
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
                    $message->set('register-token-expired');
                    return $this->request->redirect('auth');
                }
            } else {
                $message->set('register-invalid-token');
                return $this->request->redirect('auth');
            }
            // add login role
            $user->add('roles', $login);
            $message->set('register-confirmed', Message::SUCCESS);
            Sourcemap_User_Event::factory(Sourcemap_User_Event::REGISTERED, $user->id)->trigger();
            return $this->request->redirect('auth');
        } else {
            $message->set('register-invalid-token');
            return $this->request->redirect('auth');
        }
    }
}
