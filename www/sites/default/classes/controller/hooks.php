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

        if ($type == "invoice.payment_succeeded"){
            $user = ORM::factory('user')->where('customer_id', '=', $event->data->object->customer)->find();
            if ($user || $event->data->object->customer == "cus_00000000000000"){

                if ($event->data->object->customer == "cus_00000000000000"){
                     $user = ORM::factory('user')->where('username', 'ILIKE', "alex1")->find();
                }

                // Get vars
                $customer = Stripe_Customer::retrieve($user->customer_id);
                $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();

                if($user->has('roles', $channel_role))
                    $acct_level = "Sourcemap Pro";
                else
                    $acct_level = "Free";

                // Build outgoing message
                $mailer   = Email::connect();
                $subject  = Kohana::message('general', 'upgrade-payment-email-subject');
                $from     = Kohana::message('general', 'email-from');
                $msgbody  = __(Kohana::message('general', 'upgrade-payment-email-body'), array(
                    ':user' => $user->username,
                    ':payment-amount' => isset($event->object->total) ? $event->object->total : "$99.00",
                    ':payment-date' => isset($event->object->date) ? date("F j, Y", strtotime($event->object->date)) : date("F j, Y"),
                    ':card-name' => $customer->active_card->name,
                    ':card-type' => $customer->active_card->type,
                    ':card-number' => "xxxx xxxx xxxx " . $customer->active_card->last4,
                    ':card-exp' => $customer->active_card->exp_month . "/" . $customer->active_card->exp_year,
                    ':acct-level' => $acct_level,
                    ':acct-paidthru' => date("F j, Y", strtotime($customer->next_recurring_charge->date)),
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
