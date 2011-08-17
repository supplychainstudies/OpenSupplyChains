<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Register extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'register';
    
    public function action_index() {        
		$this->layout->page_title = 'Register an account on Sourcemap';
        
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template'
        );

        $f = Sourcemap_Form::load('/register');
        $f->action('create')->method('post');

        $this->template->form = $f;

        if(strtolower(Request::$method) === 'post') {
            if($f->validate($_POST)) {
                die($f->values());
                // check for username in use
                $exists = ORM::factory('user')->where('username', '=', $p['username'])->find()->loaded();
                if($exists) {
                    Message::instance()->set('That username is taken.');
                    return;
                }
                // check for email in use
                $exists = ORM::factory('user')->where('email', '=', $p['email'])->find()->loaded();
                if($exists) {
                    Message::instance()->set('An account exists for that email address.');
                    return;
                }

                $new_user = ORM::factory('user');
                $new_user->username = $p['username'];
                $new_user->email = $p['email'];
                $new_user->password = $p['password'];
                $new_user->save();

                if(!$new_user->id) {
                    Message::instance()->set('Could not complete registration. Please contact support.');
                    return $this->request->redirect('register');
                }

                //send a notification
                $subj = 'Re: Your New Account on Sourcemap.com';
                $h = md5(sprintf('%s-%s', $new_user->username, $new_user->email));
                $lid = strrev(base64_encode($new_user->username));
                $url = URL::site("register/confirm?t=$lid-$h", true);

                $msgbody = "Dear {$new_user->username},\n\n";
                $msgbody .= 'Welcome to Sourcemap! ';
                $msgbody .= "Go to the url below to activate your account.\n\n";
                $msgbody .= $url."\n\n";
                $msgbody .= "If you have any questions, please contact support@sourcemap.com.\n\n";
                $msgbody .= "Sincerely,\n";
                $msgbody .= "The Sourcemap Team\n";

                $addlheaders = "From: The Sourcemap Team <noreply@sourcemap.com>\r\n";

                try {
                    $sent = mail($new_user->email,  $subj, $msgbody, $addlheaders);
                    Message::instance()->set('Please check your email for further instructions.', Message::INFO);
                } catch (Exception $e) {
                    Message::instance()->set('Sorry, could not complete registration. Please contact support.'.$e);
                }
                return $this->request->redirect('register');
            } else {
                Message::instance()->set('Check the information below and try again.');
            }
        } else { 
		/* pass */ 
		}
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
            $user = ORM::factory('user')->where('username', '=', $username)
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
