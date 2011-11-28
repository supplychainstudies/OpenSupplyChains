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

                $email = ($_POST['email']);
                $message = ($_POST['message']);
                
                //send a notification 
				$mailer = Email::connect(); 
				$swift_msg = Swift_Message::newInstance();

				$headers = array('from' => 'Sourcemap Support Form', 'subject' => 'Sourcemap Support Request');
				
                $h = md5(sprintf('%s-%s', $email, $email));
                $lid = strrev(base64_encode("username"));
                $msgbody = $message;
                $swift_msg->setSubject('Sourcemap Support Request')
						  ->setFrom(array($email => $email))
						  ->setTo(array("info@sourcemap.com" => ''))
						  ->setBody($msgbody);

                try {
                    $sent = $mailer->send($swift_msg);
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not send message. Please contact support.');
                    echo Message::instance()->get() ? Message::instance()->render() : false;
                    return;
                }

                Message::instance()->set('Message sent.', Message::SUCCESS);
                if ($ajax){
                    echo "redirect contact/thankyou";
                    return;
                }
                else{
                    return $this->request->redirect('contact/thankyou');
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
        $this->template->set_filename('contact/thankyou');
    }

}
