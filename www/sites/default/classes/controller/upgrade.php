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

class Controller_Upgrade extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'user/upgrade';

    const MIGRATE_EMAIL = 'account-migration@sourcemap.com';

    public function action_index() {        
        $this->layout->page_title = 'Upgrade your account';

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-payments'
        );

        $f = Sourcemap_Form::load('/upgrade');
        $f->action('upgrade')->method('post');

        $this->template->form = $f;

        if(strtolower(Request::$method) === 'post') { 
             $validate= $f->validate($_POST);   
             if( $validate ) {  
                $p = $f->values();

                // get the credit card details submitted by the form
                $token = $_POST['stripeToken'];

                // set your secret key: remember to change this to your live secret key in production
                // see your keys here https://manage.stripe.com/account
                Stripe::setApiKey("ffzydGhK4dzlQFbK22Q0GGM6CSG74f9d");

                // create the charge on Stripe's servers - this will charge the user's card
                $charge = Stripe_Charge::create(array(
                  "amount" => 9995, // amount in cents, again
                  "currency" => "usd",
                  "card" => $token,
                  "description" => "payinguser@example.com")
                );

                //send a notification 
				$mailer = Email::connect(); 
				$swift_msg = Swift_Message::newInstance();

				$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => 'Re: Your Newly Upgraded Sourcemap Account');
				
                $h = md5(sprintf('%s-%s', $new_user->username, $new_user->email));
                $lid = strrev(base64_encode($new_user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);
                $msgbody = "\n";
                $msgbody .= "Dear {$new_user->username},\n\n";
                $msgbody .= "Thank you for upgrading to a channel account!";
                $msgbody .= "As a channel user, you will have access to exclusive feature that aren't available to the general public-- Most importantly, the ability to brand your channel with custom colors, logos, and banners.  Before you start mapping with your upgraded account, we recommend you fill in the newly-availble fields in your dashboard.\n\n";
                $msgbody .= "If you have any questions, please contact us at support@sourcemap.com.\n\n";
                $msgbody .= "-The Sourcemap Team\n";
                $swift_msg->setSubject('Re: Your Newly Upgraded Sourcemap Account')
						  ->setFrom(array('noreply@sourcemap.com' => 'The Sourcemap Team'))
						  ->setTo(array($new_user->email => ''))
						  ->setBody($msgbody);
					

                try { 
					$sent = $mailer->send($swift_msg);
                    Message::instance()->set('Activation email sent.');
                    return $this->request->redirect('register/thankyou');
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete registration. Please contact support.');
                } 

                if(isset($p['sourcemaporg_account']) && $p['sourcemaporg_account']) {
                    try {
	 					$swift_msg = Swift_Message::newInstance();
						$msgbody = "\n";
                        $msgbody .= 'New user '.$new_user->username.' requested migration from Sourcemap.org.'."\r\n\r\n";
                        $msgbody .= "Sourcemap.org Account Name: {$p['sourcemaporg_account']}\r\n";
                        $msgbody .= "New User Email: {$new_user->email}\r\n\r\n";
                        $msgbody .= "Go to: ".URL::site('user/'.$new_user->id, true)." to view this user's profile.\r\n";
                        $msgbody .= "Go to: ".URL::site('admin/users/'.$new_user->id, true)." to view this user's details.\r\n";
						$swift_msg->setSubject("MIGRATE REQUEST: ".$p['sourcemaporg_account'])
								  ->setFrom(array('noreply@sourcemap.com' => 'The Sourcemap Team'))
								  ->setTo(array(self::MIGRATE_EMAIL => ''))
								  ->setBody($msgbody);
                        $sent = $mailer->send($swift_msg); 
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
