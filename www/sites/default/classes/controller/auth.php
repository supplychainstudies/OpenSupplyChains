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

class Controller_Auth extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
    public $template = 'auth';

    public function action_index() {

        $this->layout->page_title = Auth::instance()->get_user() ? 'Signed in' : 'Sign in';
        if(Auth::instance()->get_user()) {
            $this->template->current_user_id = Auth::instance()->get_user();
            $this->template->current_user = ORM::factory('user', Auth::instance()->get_user());
               $this->request->redirect('home/');
        } else {
            $this->template->current_user_id = false;
            $this->template->current_user = false;
            $f = Sourcemap_Form::load('/auth');
            $f->action('auth')->method('post');
            $this->template->login_form = $f;
        }
        
        if(strtolower(Request::$method) === 'post') {
            if($f->validate($_POST)) {
                // Login
                if(Auth::instance()->login($_POST['username'], $_POST['password'])) {
                } else {
                    Message::instance()->set('Invalid username or password.', Message::ERROR);
                    $this->request->redirect('auth');
                }
                
                if (!empty($_POST->next)) {
                    $this->request->redirect($_POST->next);
                } else {
                    $this->request->redirect('home/');
                }
       
            } else {
                Message::instance()->set('Correct the errors below.');
            }
        }
    
        $this->layout->scripts = array(
            'sourcemap-core'
        );

    }

    public function action_logout() {
        $auth = Auth::instance();
        if($auth->logged_in()) {
            $auth->logout(true);
        }
        $this->request->redirect('');
    }


    public function action_forgot() {
   
        $this->template = View::factory('auth/forgot_password');
        $this->layout->page_title = "Forgot password";
        $post = Validate::factory($_POST);
        $post ->rule('email', 'not_empty')
            ->rule('email', 'validate::email')
            ->filter(true, 'trim');
       
        if(strtolower(Request::$method) === 'post' && $post->check()){
            $post = (object)$post->as_array();
            $email = $post->email;
            $user = ORM::factory('user')->where('email', '=', $email)->find();
            if($user->loaded()) {
                $s = sprintf('%s-%s-%s-%s-%s', $user->id, $user->username, $user->email, $user->last_login, $user->password);
                $h = md5($s);
                $un = strrev(base64_encode($user->username));
                $em = strrev(base64_encode($user->email));
                $t = sprintf('%s-%s-%s', $un, $h, $em);
                if($this->email_reset_ticket($user->username, $user->email, $t)) {
                    $this->template->email_sent = true;
                }
                $this->request->redirect('auth');
            } else {
                Message::instance()->set('We don\'t recognize that address.');
                $this->request->redirect('auth/forgot');
            }
        } else {
            // pass
        }
    }

    public function email_reset_ticket($username, $email, $ticket) {
        //$email_vars = array('username' => $username, 'password' => $temp_password);
        $to = $email;

        $subject = 'Password Reset Request';

        $body = "Dear {$username},\n";
        $body .= <<<EREIAM

If you asked us to reset the password associated with your user account, please visit the URL below:


EREIAM;
        $body .= URL::site('auth/reset?t='.$ticket, true);
        $body .= <<<EREIAM

If you believe that someone else made this request, please contact us as soon as possible.
Thank you for using Open Supply Chains!
EREIAM;

        $addlheaders = "From: Open Supply Chains\r\n";

        $sent = false;
        try {
            //Sourcemap_Email_Template::send_email($to, $subject, $body);
            $sent = mail($email, $subject, $body, $addlheaders);
            Message::instance()->set('Please check your email for further instructions.', Message::INFO);
        } catch (Exception $e) {
            Message::instance()->set('Sorry, could not send an email.');
        }
        return $sent;
    }
    

    public function action_reset() {

        $this->template = View::factory('auth/reset_password');

        $current_user = Auth::instance()->get_user();

        $post = Validate::factory($_POST);
        $post->rule('new', 'not_empty')
            ->rule('new_confirm', 'not_empty')
            ->rule('new_confirm', 'matches', array('new'))
            ->filter(true, 'trim');

        if(strtolower(Request::$method) === 'post') {
 
            // make sure the user has a valid reset ticket or is logged in.
            $tregex = '/[A-Za-z0-9\+\/=]+-[A-Fa-f0-9]{32}-[A-Za-z0-9\+\/=]+/';
            if(!$current_user && isset($_POST['t']) && preg_match($tregex, $_POST['t'])) {
                list($un, $h, $em) = explode('-', $_POST['t']);
                $un = base64_decode(strrev($un));
                $em = base64_decode(strrev($em));
                $user = ORM::factory('user')->where('email', '=', $em)->find();
                if($user->loaded()) {
                    if($user->username == $un) {
                        $tgth = md5(sprintf('%s-%s-%s-%s-%s', $user->id, $user->username, $user->email, $user->last_login, $user->password));
                        if($tgth === $h) {
                            $current_user = $user;
                            if($post->check()) {
                                $user->password = $post['new'];
                                $user->save();
                                Auth::instance()->login($user->username, $post['new']);
                                Message::instance()->set('Password reset.', Message::SUCCESS);
                                // TODO: notify via email of reset?
                                return $this->request->redirect('auth');
                            } else {
                                // pass
                            }
                        } else {
                            Message::instance()->set('That token has expired.  Please create a new request.');
                            return $this->request->redirect('auth');
                        }
                    } else {
                        Message::instance()->set('Password reset failed.  Please contact support for assistance.');
                        return $this->request->redirect('auth');
                    }
                } else {
                    Message::instance()->set('We don\'t have this address on record.  Please create a new request.');
                    return $this->request->redirect('auth');
                }
            } 
            
            if(!$current_user) {
                Message::instance()->set('You can\'t do that.');
                $this->request->redirect('auth');
            } elseif($post->check()) { // && $tgth === $current_user->password) {
                // user is logged in...reset password...
                // TODO: notify user via email?
                $current_user->password = $post['new'];
                $current_user->save();
                Message::instance()->set('Your password has been reset.', Message::SUCCESS);
                $this->request->redirect('auth');
            } else {
                Message::instance()->set('Please try again.', Message::ERROR);
                if(isset($_POST['t'])) {
                    $this->request->redirect('auth/reset?t='.$_POST['t']);
                } else {
                    $this->request->redirect('auth/reset');
                }
            }

        } else {
            
            $get = Validate::factory($_GET);
            $get->rule('t', 'not_empty')
                ->rule('t', 'regex', array('/[A-Za-z0-9\+\/=]+-[A-Fa-f0-9]{32}-[A-Za-z0-9\+\/=]+/'));

            if(!$current_user && isset($_GET['t'])) {
                if($get->check()) {
                    list($un, $h, $em) = explode('-', $get['t']);
                    $un = base64_decode(strrev($un));
                    $em = base64_decode(strrev($em));
                    $user = ORM::factory('user')->where('email', '=', $em)->find();
                    if($user->loaded()) {
                        if($user->username == $un) {
                            $tgth = md5(sprintf('%s-%s-%s-%s-%s', $user->id, $user->username, $user->email, $user->last_login, $user->password));
                            if($tgth === $h) {
                                $current_user = $user;
                                $this->template->ticket = $get['t'];
                            } else {
                                Message::instance()->set('That token has expired.');
                                return $this->request->redirect('auth');
                            }
                        } else {
                            Message::instance()->set('That didn\'t work.');
                            return $this->request->redirect('auth');
                        }
                    } else {
                        return $this->request->redirect('auth');
                    }
                } else {
                    Message::instance()->set('That didn\'t work.');
                    return $this->request->redirect('auth');
                }
            } elseif(!$current_user) {
                Message::instance()->set('You can\'t do that.');
                $this->request->redirect('auth');
            }

           

        }
    }


   

  }
