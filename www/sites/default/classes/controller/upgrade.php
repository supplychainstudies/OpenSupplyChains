<?php
/* Copyright (C) Sourcemap 2011 */

class Controller_Upgrade extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'user/upgrade';
    public $ssl_required = true;


    public function action_index() {       

        $this->layout->page_title = 'Upgrade your account';

        $this->layout->scripts = array(
            'sourcemap-payments'
        );

        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }

        if(strtolower(Request::$method) === 'post') {
            $ajax = isset($_POST["_form_ajax"]) ? 'true' : 'false';
            
            // Create message object
            $message = new Message($ajax);

            // This assumes that we've already passed CC validation.  
            // So, full steam ahead...
            $p = $_POST;
            try{
                try{
                    Stripe_Plan::retrieve("channel");
                } catch (Exception $e) {
                    // create plan if it doesn't exist
                    Stripe_Plan::create(array(
                      "amount" => 9900,
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
                $message->set('upgrade-cc-failed' . $e);
                $this->request->redirect('home/');
            } 

            // Build outgoing message
            $mailer   = Email::connect();
            $subject  = Kohana::message('general', 'upgrade-email-subject');
            $from     = Kohana::message('general', 'email-from');
            $msgbody  = __(Kohana::message('general', 'upgrade-email-body'), array(
                ':user' => $user->username
            ));
            $swift_msg = Swift_Message::newInstance();
            $swift_msg->setSubject($subject)
                      ->setFrom($from)
                      ->setTo(array($user->email => ''))
                      ->setBody($msgbody);
            
            // Send notifcation
            try { 
                $sent = $mailer->send($swift_msg);
                $message->set('upgrade-email-sent');
                
                // Set channel status
                $channel_role = ORM::factory('role', array('name' => 'channel'));
                $user->add('roles', $channel_role)->save();
                
                // Redirect
                if ($ajax)
                    echo "redirect upgrade/thankyou ";
                else
                    return $this->request->redirect('upgrade/thankyou');
                return;
             } catch (Exception $e) {
                 $message->set('upgrade-generic');
             } 

            return $this->request->redirect('register');
        } else { 
        /* pass */ 
        }
        
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if($user->has('roles', $channel_role)) {
            Message::instance()->set('upgrade-already-done');
            $this->request->redirect('home');
        } 
    
    }
    
    public function action_payments() {
        $this->template = new View('user/payments');

        $this->layout->page_title = 'View your account payments';

        $this->layout->scripts = array(
            'sourcemap-payments'
        );
        
        // Create message object
        $message = new Message();

        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }

        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if(!($user->has('roles', $channel_role))) {
            $message->set('upgrade-havent-upgraded');
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
            $this->template->status = "Sourcemap Pro";
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
            $message = new Message($ajax);
            $message->set('upgrade-havent-upgraded');
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
        // Create message object
        $message = new Message;
        
        $this->template = new View('user/renew');
        $this->layout->page_title = 'Renew your account';
        if(!($user = Auth::instance()->get_user())) {
            $this->request->redirect('auth');
        }
        
        $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
        if(!($user->has('roles', $channel_role))) {
            $message->set('upgrade-havent-upgraded');
            $this->request->redirect('user/upgrade');
        } 
        
        $f = Sourcemap_Form::load('/renew');
        $f->action('upgrade/renew')->method('post');
        $this->template->form = $f;
        
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
            $message->set('upgrade-no-cc');
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
                        $message->set('upgrade-lookup-failed');
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
                    $message->set('upgrade-cc-failed');
                    $this->request->redirect('home/');
                } 

                // Build outgoing message
                $mailer   = Email::connect();
                $subject  = Kohana::message('general', 'renew-email-subject');
                $from     = Kohana::message('general', 'email-from');
                $msgbody  = __(Kohana::message('general', 'renew-email-body'), array(
                    ':user' => $user->username
                ));
                $swift_msg = Swift_Message::newInstance();
                $swift_msg->setSubject($subject)
                          ->setFrom($from)
                          ->setTo(array($user->email => ''))
                          ->setBody($msgbody);

                // Send message
                try { 
					$sent = $mailer->send($swift_msg);
                    $message->set('upgrade-email-sent');
                    
                    //set channel status
                    $channel_role = ORM::factory('role', array('name' => 'channel'));
                    $user->add('roles', $channel_role)->save();
                    
                    return $this->request->redirect('upgrade/renew/thankyou');
                } catch (Exception $e) {
                    $message->set('upgrade-generic');
                } 

                return $this->request->redirect('register');
            } else {
                $message->set('form-validation-fail');
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
