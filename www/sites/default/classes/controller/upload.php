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
	
	$this->request->redirect("https://www.google.com/accounts/AuthSubRequest?scope=http%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F&session=1&secure=0&session=1&next=http%3A%2F%2Flocalhost%2Fsmap%2Ftrunk%2Fwww%2Fupload%2Fauthenticate");

    }

    
    public function action_authenticate() {

	$this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-working', 'sourcemap-upload'
	    );
        $this->layout->styles = array(
            'assets/styles/style.css', 
            'assets/styles/sourcemap.less?v=2'
        );

	
	$headers = array();
	$headers[] = 'GET /accounts/AuthSubSessionToken HTTP/1.1';
	$headers[] = 'Authorization: AuthSub token="'.$_GET['token'].'"';
	$headers[] = 'Accept: text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2';
	$headers[] = 'Connection: keep-alive;';
	$headers[] = 'GData-Version: 3.0';

	
	$curl = curl_init('https://www.google.com/accounts/AuthSubSessionToken');
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $curl, CURLOPT_HEADER, true );
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	$response = curl_exec( $curl );private

	
	// Get the Auth string and save it
	preg_match("/Token=1\/([a-z0-9_\-]+)/i", $response, $matches);
	$auth = $matches[1]; 

	echo "The auth string is: ".$auth;
        // Include the Auth string in the headers
        // Together with the API version being used
	$headers = array(
	    "Authorization: GoogleLogin auth=".$auth,
	    "GData-Version: 3.0",
	    );

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://spreadsheets.google.com/feeds/cells/0Aqwz6ZHrexb7dHNBa0tsVDhlX1N5MkVrV3FxczE2cmc/od6/private/full");
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POST, false);
	
	$response = curl_exec($curl);
	curl_close($curl);
	print_r($response);
		
	$response = simplexml_load_string($response);
	print($response);
	
	
    }
  }
