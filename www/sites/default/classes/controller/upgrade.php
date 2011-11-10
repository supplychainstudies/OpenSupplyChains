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

        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }

        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role)) {
            Message::instance()->set("You've already upgraded your account.");
            $this->request->redirect('home');
        } 

        if(strtolower(Request::$method) === 'post') { 
             $validate= $f->validate($_POST);   
             if( $validate ) {  
                $p = $f->values();
                try{
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
                } catch (Exception $e) {
                    Message::instance()->set('Please check the information below.');
                } 

                //success! set channel status
                $channel_role = ORM::factory('role', array('name' => 'channel'));
                $user->add('roles', $channel_role)->save();

                //TODO: create customer object and invoice object
                // https://stripe.com/docs/api

                //send a notification 
				$mailer = Email::connect(); 
				$swift_msg = Swift_Message::newInstance();

				$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => 'Re: Your Newly Upgraded Sourcemap Account');
				
                $h = md5(sprintf('%s-%s', $user->username, $user->email));
                $lid = strrev(base64_encode($user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);
                $msgbody = "\n";
                $msgbody .= "Dear {$user->username},\n\n";
                $msgbody .= "Thank you for upgrading to a channel account!";
                $msgbody .= "As a channel user, you will have access to exclusive feature that aren't available to the general public-- Most importantly, the ability to brand your channel with custom colors, logos, and banners.  Before you start mapping with your upgraded account, we recommend you fill in the newly-availble fields in your dashboard.\n\n";
                $msgbody .= "If you have any questions, please contact us at support@sourcemap.com.\n\n";
                $msgbody .= "-The Sourcemap Team\n";
                $swift_msg->setSubject('Re: Your Newly Upgraded Sourcemap Account')
						  ->setFrom(array('noreply@sourcemap.com' => 'The Sourcemap Team'))
						  ->setTo(array($user->email => ''))
						  ->setBody($msgbody);
					

                try { 
					$sent = $mailer->send($swift_msg);
                    Message::instance()->set('Email confirmation sent.');
                    return $this->request->redirect('user/thankyou');
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete account upgrade. Please contact support.');
                } 

                return $this->request->redirect('register');
            } else {
                Message::instance()->set('Check the information below and try again.');
            }
        } else { 
        /* pass */ 
        }
    }
    
    public function action_payments() {
        $this->template = new View('user/payments');

        $this->layout->page_title = 'View your account payments';

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-payments'
        );

        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }

        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if(!($user->has('roles', $channel_role))) {
            Message::instance()->set("You haven't upgraded your account yet.");
            $this->request->redirect('user/upgrade');
        } 
        
        //TODO: retrieve customer object and invoice objects
        // https://stripe.com/docs/api
    }
}
