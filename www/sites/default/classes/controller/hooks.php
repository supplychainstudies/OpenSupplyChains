<?php
/* Copyright (C) Sourcemap 2011 */
// used for stripe

class Controller_hooks extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'wp';

    public function action_index() {        
        $this->auto_render = FALSE;
        echo "hey!";
    }

    public function action_invoice(){
        $this->auto_render = FALSE;

        // Grab event from Stripe's webhook req
        $body = @file_get_contents('php://input');
        $event = json_decode($body);
        $type = isset($event->type) ? $event->type : "none";

        if ($type == "invoice.created"){
            if ($user = ORM::factory('user')->where('customer_id', 'ILIKE', $event->data->object->customer || $event->data->object->customer == "cus_00000000000000")){
    
                // Get vars
                $customer = Stripe_Customer::retrieve($user->customer_id);
                
                // Build outgoing message
                $mailer   = Email::connect();
                $subject  = Kohana::message('general', 'upgrade-payment-email-subject');
                $from     = Kohana::message('general', 'email-from');
                $msgbody  = __(Kohana::message('general', 'upgrade-email-body'), array(
                    ':user' => $user->username,
                    ':payment-amount' => "$" . ($event->object->subtotal / 100),
                    ':payment-date' => strtotime($event->object->date),
                    ':card-name' => $customer->active_card->name,
                    ':card-type' => $customer->active_card->type,
                    ':card-number' => "xxxx xxxx xxxx " . $customer->active_card->last4,
                    ':card-exp' => $customer->active_card->exp_month . "/" . $customer->active_card->exp_year,
                    ':account-level' => $acct_level,
                    ':acct-paidthru' => strtotime($customer->next_recurring_charge->date),
                ));
                $swift_msg = Swift_Message::newInstance();
                $swift_msg->setSubject($subject)
                          ->setFrom($from)
                          ->setTo(array($user->email => ''))
                          ->setBody($msgbody);

                // Send notifcation
                try {
                    $sent = $mailer->send($swift_msg);
                } catch (Exception $e) {
                    $message->set('upgrade-generic');
                }

                $this->template->content = "";
                echo "success!  email sent.";
            } else {
                $this->request->status = 500;
                echo "couldn't find user.";
            }
        } else {
            $this->request->status = 500;
            echo "unexpected event type";
        }
    }
}
