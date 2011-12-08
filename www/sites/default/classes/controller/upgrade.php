<?php
/* Copyright (C) Sourcemap 2011 */

class Controller_Upgrade extends Sourcemap_Controller_Layout {

    public $force_ssl = TRUE;

    public $layout = 'base';
    public $template = 'user/upgrade';
        
    public $ssl_required = true;

    const MIGRATE_EMAIL = 'account-migration@sourcemap.com';

    public function action_index() {        

        $this->layout->page_title = 'Upgrade your account';

        $this->layout->scripts = array(
            'sourcemap-payments'
        );

        $f = Sourcemap_Form::load('/upgrade');
        $f->action('upgrade')->method('post');

        $this->template->form = $f;

        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }


        if(strtolower(Request::$method) === 'post') {
             $ajax = isset($_POST["_form_ajax"]) ? 'true' : 'false';
             if (!$f->validate($_POST)){
                 $errors = $f->errors();
                 foreach($errors as $error){
                     Message::instance()->set($error[0]);
                 }

                 echo $ajax ? Message::instance()->render() : "";
                 return;
             } else {
                $p = $f->values();
                try{
                    // set your secret key: remember to change this to your live secret key in production
                    // see your keys here https://manage.stripe.com/account
                    Stripe::setApiKey(Kohana::config('apis')->stripe_api_secret_key);

                    try{
                        Stripe_Plan::retrieve("channel");
                    } catch (Exception $e) {
                        // create plan if it doesn't exist
                        Stripe_Plan::create(array(
                          "amount" => 9995,
                          "interval" => "year",
                          "name" => "Channel",
                          "currency" => "usd",
                          "id" => "channel")
                        );
                    }
                    
                    // get the credit card details submitted by the form
                    $token = $_POST['stripeToken'] ? $_POST['stripeToken'] : false;

                    try{
                        // do we already have a customer ID?  then we're renewing 
                        $cu = Stripe_Customer::retrieve($user->customer_id);
                        $cu->updateSubscription(array("plan" => "channel"));
                    } catch (Exception $e) {
                        // otherwise create new stripe customer based on existing username
                        $customer = Stripe_Customer::create(array(
                            "description" => $user->username,
                            "plan" => "channel",
                            "card" => $token
                        ));

                        $user->customer_id = $customer->id;
                        $user->save();
                    }

                } catch (Exception $e) {
                    Message::instance()->set('Please check your credit card information and try again.' . $e);
                    $this->request->redirect('home/');
                } 

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
                    
                    //set channel status
                    $channel_role = ORM::factory('role', array('name' => 'channel'));
                    $user->add('roles', $channel_role)->save();

                    if ($ajax){
                        echo "redirect upgrade/thankyou ";
                        return;
                    }
                    else{
                        return $this->request->redirect('upgrade/thankyou');
                    }
                    
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete account upgrade. Please contact support.');
                } 

                return $this->request->redirect('register');
            } 
        } else { 
        /* pass */ 
        }
        
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role)) {
            Message::instance()->set("You've already upgraded your account.");
            $this->request->redirect('home');
        } 
    
    }
    
    public function action_payments() {
        $this->template = new View('user/payments');

        $this->layout->page_title = 'View your account payments';

        $this->layout->scripts = array(
            'sourcemap-payments'
        );

        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }

        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if(!($user->has('roles', $channel_role))) {
            Message::instance()->set("You haven't upgraded your account yet.");
            $this->request->redirect('user/upgrade');
        } 
        
        $customer = Stripe_Customer::retrieve($user->customer_id);

        $this->template = new View('user/payments');
        $this->template->username = $user->username;
        $this->template->card_name = $customer->active_card->name;
        $this->template->card = "xxxx xxxx xxxx " . $customer->active_card->last4;
        $this->template->card_type = $customer->active_card->type;
        $this->template->exp_month= $customer->active_card->exp_month;
        $this->template->exp_year= $customer->active_card->exp_year;
        $this->template->thru = strtotime($customer->next_recurring_charge->date);
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role)) {
            $this->template->status = "Channel";
        } 
        else{
            $this->template->status = "Free";
        }

        $payments = Stripe_Invoice::all(array(
          "customer" => $user->customer_id,
          "count" => 999)
        );

        // add card data to payments
        $payment_data = array(); 
        foreach($payments->data as $payment){
            $charge = Stripe_Charge::retrieve($payment->charge);
            $payment->card = $charge->card;
            $payment_data[] = $payment;
        }

        $this->template->payments = $payment_data;
        
        $this->template->user = $user;
    }
    
    public function action_thankyou() {
        $this->layout->page_title = 'Thank you!';
        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }
        
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if(!($user->has('roles', $channel_role))) {
            Message::instance()->set("You haven't upgraded your account yet.");
            $this->request->redirect('user/upgrade');
        } 

        $customer = Stripe_Customer::retrieve($user->customer_id);

        $this->template = new View('user/thankyou');
        $this->template->username = $user->username;
        $this->template->card_name = $customer->active_card->name;
        $this->template->card = "xxxx xxxx xxxx " . $customer->active_card->last4;
        $this->template->card_type = $customer->active_card->type;
        $this->template->exp_month= $customer->active_card->exp_month;
        $this->template->exp_year= $customer->active_card->exp_year;
        $this->template->thru = strtotime($customer->next_recurring_charge->date);
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role)) {
            $this->template->status = "Channel";
        } 
        else{
            $this->template->status = "Free";
        }
        $this->template->user = $user;
    }

    public function action_renew() {
        $this->template = new View('user/renew');
        $this->layout->page_title = 'Renew your account';
        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }
        
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if(!($user->has('roles', $channel_role))) {
            Message::instance()->set("You haven't upgraded your account yet.");
            $this->request->redirect('user/upgrade');
        } 
        
        $f = Sourcemap_Form::load('/renew');
        $f->action('upgrade/renew')->method('post');
        $this->template->form = $f;
        
        try{
            $customer = Stripe_Customer::retrieve($user->customer_id);
            $f = Sourcemap_Form::load('/renew');
            $f->action('upgrade/renew')->method('post');
            $this->template->renew_form = $f;
            
            $this->template->card_name = $customer->active_card->name;
            $this->template->card = "xxxx xxxx xxxx " . $customer->active_card->last4;
            $this->template->card_type = $customer->active_card->type;
            $this->template->exp_month= $customer->active_card->exp_month;
            $this->template->exp_year= $customer->active_card->exp_year;
            $this->template->thru = strtotime($customer->next_recurring_charge->date);
        } catch (Exception $e){
            Message::instance()->set("No credit card on file.  Please contact support.");
            $this->request->redirect('/upgrade/renew');
        }
        
        if(strtolower(Request::$method) === 'post') { 
             $validate= $f->validate($_POST);   
             if( $validate ) {  
                $p = $f->values();
                try{
                    try{
                        Stripe_Plan::retrieve("channel");
                    } catch (Exception $e) {
                        Message::instance()->set("There was a problem.  Please contact support.");
                        $this->request->redirect('/upgrade/renew');
                    }
 
                    // use existing card on file?
                    if ($_POST['existing-card'] == "on"){
                        // renew account with card on file 
                        $customer->updateSubscription(array("plan" => "channel"));
                    } else {
                        // get the credit card details submitted by the form
                        $token = $_POST['stripeToken'] ? $_POST['stripeToken'] : false;
                        $customer->updateSubscription(array(
                            "plan" => "channel",
                            "card" => $token
                        ));
                    }

                } catch (Exception $e) {
                    Message::instance()->set('Please check the credit card information below.' . $e);
                    $this->request->redirect('home/');
                } 

                //send a notification 
				$mailer = Email::connect(); 
				$swift_msg = Swift_Message::newInstance();

				$headers = array('from' => 'The Sourcemap Team <noreply@sourcemap.com>', 'subject' => 'Re: Your Sourcemap Channel Renewal');
				
                $h = md5(sprintf('%s-%s', $user->username, $user->email));
                $lid = strrev(base64_encode($user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);
                $msgbody = "\n";
                $msgbody .= "Dear {$user->username},\n\n";
                $msgbody .= "Thank you for renewing your channel!";
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
                    
                    //set channel status
                    $channel_role = ORM::factory('role', array('name' => 'channel'));
                    $user->add('roles', $channel_role)->save();
                    
                    return $this->request->redirect('upgrade/renew/thankyou');
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete account upgrade. Please contact support.');
                } 

                return $this->request->redirect('register');
            } else {
                Message::instance()->set('Check the information below and try again.');
                $this->request->redirect('upgrade/renew');
            }
        }
       
        
        $this->template->username = $user->username;
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role)) {
            $this->template->status = "Channel";
        } 
        else{
            $this->template->status = "Free";
        }
        $this->template->user = $user;
    }
}
