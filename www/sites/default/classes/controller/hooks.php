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

    public function action_email(){
        $this->auto_render = FALSE;
        
        // Build outgoing message
        $mailer   = Email::connect();
        $subject  = Kohana::message('general', 'upgrade-email-subject');
        $from     = Kohana::message('general', 'email-from');
        $msgbody  = "Test"; 
        $swift_msg = Swift_Message::newInstance();
        $swift_msg->setSubject($subject)
                  ->setFrom($from)
                  ->setTo(array("alexander.ose@gmail.com" => ''))
                  ->setBody($msgbody);

        // Send notifcation
        try {
            $sent = $mailer->send($swift_msg);
         } catch (Exception $e) {
             $message->set('upgrade-generic');
         }

        $this->template->content = "";
        echo "yay";
    }
}
