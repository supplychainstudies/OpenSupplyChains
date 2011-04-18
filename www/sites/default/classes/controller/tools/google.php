<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Tools_Google extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'google';

    public function action_index() {
        if(Session::instance()->get('g_oauth_access_token')) {
            $this->request->redirect('/tools/google/list/');
        }
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $oauth->_req_token_callback = Url::site('/tools/google/auth', true);
        $auth_token = $oauth->get_req_token();
        if(!$auth_token) throw new Exception('Could not obtain auth token.');
        $secret = $auth_token['oauth_token_secret'];
        Session::instance()->set('g_oauth_token_secret', $secret);
        unset($auth_token['oauth_token_secret']);
        $this->request->redirect(
            Google_Oauth::OAUTH_BASE.Google_Oauth::OAUTH_AUTHTOKEN.
                Url::query($auth_token)
        );
    }

    
    public function action_auth() {

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 
            'sourcemap-working', 'sourcemap-upload'
        );
        $this->layout->styles = array(
            'assets/styles/style.css', 
            'assets/styles/sourcemap.less'
        );

        if(isset($_GET['oauth_token'], $_GET['oauth_verifier']) && ($secret = Session::instance()->get('g_oauth_token_secret'))) {
            $auth_tok = array(
                'oauth_token' => $_GET['oauth_token'],
                'oauth_verifier' => $_GET['oauth_verifier'],
                'oauth_token_secret' => $secret
            );
        } else {
            Message::instance()->set('Invalid OAuth token or identifier. Try again.');
            $this->redirect('/tools/google/');
        }
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $acc_token = $oauth->get_acc_token($auth_tok);
        Session::instance()->set('g_oauth_access_token', $acc_token);
        $this->request->redirect('/tools/google/list');
    }

    public function action_list() {
        if(!($acc_token = Session::instance()->get('g_oauth_access_token'))) {
            Message::instance()->set('You haven\'t given us permission to fetch spreadsheets.');
            $this->request->redirect('/tools/google/');
        }
        $list = Google_Spreadsheets::get_list($acc_token);
        $this->template = View::factory('google/list');
        $this->template->list = $list;
    }

    public function action_import() {
        if(!isset($_GET['k'])) {
            Message::instance()->set('Spreadsheet key required.');
            $this->request->redirect('/tools/google/list');
        }
        die('get the effing sheet.');
    }

    public function action_worksheets() {
        if(!($acc_token = Session::instance()->get('g_oauth_access_token'))) {
            Message::instance()->set('You haven\'t given us permission to fetch spreadsheets.');
            $this->request->redirect('/tools/google/');
        }
        if(!isset($_GET['k'])) {
            Message::instance()->set('Worksheet key required.');
            $this->request->redirect('/tools/google/list');
        }
        $worksheets = Google_Spreadsheets::get_sheet_worksheets($acc_token, $_GET['k']);
        if(!$worksheets) {
            Message::instance()->set('Could not fetch worksheets for that spreadsheet.');
            $this->request->redirect('/tools/google/list');
        }
        $this->template = View::factory('google/worksheets');
        $this->template->worksheets = $worksheets;
        $this->template->spreadsheet_key = $_GET['k'];
    }
  }
