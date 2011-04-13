<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Upload extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'upload';

    public function action_index() {
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $oauth->_req_token_callback = Url::site('/upload/auth', true);
        $auth_token = $oauth->get_req_token();
        if(!$auth_token) throw new Exception('Could not obtain auth token.');
        $secret = $auth_token['oauth_token_secret'];
        Session::instance()->set('oauth_token_secret', $secret);
        unset($auth_token['oauth_token_secret']);
        $this->request->redirect(
            Google_Oauth::OAUTH_BASE.Google_Oauth::OAUTH_AUTHTOKEN.
                Url::query($auth_token)
        );
    }

    
    public function action_auth() {
        if(isset($_GET['oauth_token'], $_GET['oauth_verifier']) && ($secret = Session::instance()->get('oauth_token_secret'))) {
            $auth_tok = array(
                'oauth_token' => $_GET['oauth_token'],
                'oauth_verifier' => $_GET['oauth_verifier'],
                'oauth_token_secret' => $secret
            );
        } else {
            throw new Exception('Invalid token/verifier.');
        }
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $acc_token = $oauth->get_acc_token($auth_tok);


        die(print_r($acc_token, true));

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 
            'sourcemap-working', 'sourcemap-upload'
        );
        $this->layout->styles = array(
            'assets/styles/style.css', 
            'assets/styles/sourcemap.less'
        );


        $headers = array();
        //no! $headers[] = 'GET /accounts/AuthSubSessionToken HTTP/1.1';
        $headers = array(
            'Authorization' => 'AuthSub token="'.$_GET['token'].'"',
            #'Accept' => 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2',
            #'Connection' => 'keep-alive;',
            'GData-Version: 3.0'
        );


        /*$curl = curl_init('https://www.google.com/accounts/AuthSubSessionToken');
        curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        $response = curl_exec( $curl );private*/

        $url = 'https://www.google.com/accounts/AuthSubSessionToken';
        $response = Sourcemap_Http_Client::do_get($url, array(), $headers);


        // Get the Auth string and save it
        //preg_match("/Token=1\/([a-z0-9_\-]+)/i", $response, $matches);
        //$auth = $matches[1];
        $access_token = false;
        if($response->status_ok()) {
            $returned = null;
            parse_str($response->body, $returned);
            if(!isset($returned['Token']))  {
                throw new Exception('Token missing in response.');
            } else {
                $access_token = $returned['Token'];
            }
        } else throw new Exception('Could not acquire token.');

        // Include the Auth string in the headers
        // Together with the API version being used
        $headers = array(
            'Authorization' => "GoogleLogin auth=$access_token",
            'GData-Version' => '3.0'
        );

        $url = 'https://spreadsheets.google.com/feeds/spreadsheets/private/full';
        $response = Sourcemap_Http_Client::do_get($url, null, $headers);
        if($response->status_ok()) {
            die(print_r($response, true));
        } else {
            die(print_r($response, true));
        }
        /*$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://spreadsheets.google.com/feeds/cells/0Aqwz6ZHrexb7dHNBa0tsVDhlX1N5MkVrV3FxczE2cmc/od6/private/full");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, false);*/

        /*$response = curl_exec($curl);
        curl_close($curl);
        print_r($response);*/

        $response = simplexml_load_string($response);
        print($response);


        }
  }
