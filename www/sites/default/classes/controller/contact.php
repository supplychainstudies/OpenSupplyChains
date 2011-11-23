<?php
/* Copyright (C) Sourcemap 2011 */

class Controller_Contact extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'contact';

    const MIGRATE_EMAIL = 'account-migration@sourcemap.com';

    public function action_index() { 
        if(Auth::instance()->get_user()) {
            $user = ORM::factory('user', Auth::instance()->get_user());
        }

        $this->layout->page_title = 'Contact us';

        $f = Sourcemap_Form::load('/contact');
        $f->action('contact')->method('post');

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

                //send a notification 
				$mailer = Email::connect(); 
				$swift_msg = Swift_Message::newInstance();

				$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => 'Re: Your New Sourcemap Account');
				
                $h = md5(sprintf('%s-%s', "username", "alex@alexose.com"));
                $lid = strrev(base64_encode("username"));
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
						  ->setTo(array("alex@alexose.com" => ''))
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
