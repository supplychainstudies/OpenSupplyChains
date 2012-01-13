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

class Sourcemap_Controller_Service extends Controller_REST {

    const HDR_CACHE_HIT = 'X-Sourcemap-Cache-Hit';

    const HDR_API_KEY = 'X-Sourcemap-API-Key';
    const HDR_API_TOKEN = 'X-Sourcemap-API-Token';
    const HDR_API_DATE = 'Date';
    const API_DATE_MARGIN = 30; // seconds
    
    public $_action_map = array(
        'GET' => 'get',
        'PUT' => 'put',
        'POST' => 'post',
        'DELETE' => 'delete',
        'HEAD' => 'head'
    );

    public $response = null;
    
    // Serialization parameters
    public $_format = 'json';
    public $_default_format = 'json';
    public $_default_content_type = 'application/json';
    public $_jsonp_callback = 'console.log';
    public $_content_types = array(
        'json' => 'application/json',
        'jsonp' => 'text/javascript',
        'php' => 'application/vnd.php.serialized',
		'geojson' => 'application/geo+json',
		'kml' => 'application/vnd.google-earth.kml+xml',
        'form' => array(
            'application/x-www-form-urlencoded',
            'multipart/form-data'
        ),
        'csv' => 'text/csv',
		'xls' => 'application/xls',
		'xlsx' => 'application/xlsx',
		'png' => 'image/png',		
		'jpeg' => 'image/jpeg'		
    );

    public $_cache_hit = false;

    public $_current_user = false;

    public $_api_user = false;

    // Collection options
    public $_max_page_sz = 25;
    public $_default_page_sz = 25;
    public $_search_params = array();
    public $_sort_fields = array();

    public function before() {
        $pbefore = parent::before();
        if(Request::$method === 'POST') {
            $this->request->posted_content_type = $this->_req_content_type();
            $this->request->posted_raw = file_get_contents('php://input');
            $this->request->posted_data = $this->_unserialize($this->request->posted_raw);
        } elseif(Request::$method === 'PUT') {
            $this->request->put_content_type = $this->_req_content_type();
            $this->request->put_raw = Request::$raw_req_body;
            $this->request->put_data = $this->_unserialize($this->request->put_raw);
        } else {
            if(isset($_GET['f'])) {
                $this->_format = $_GET['f'];
            } elseif($this->request->param('format')) {
                $this->_format = $this->request->param('format');
            } else {
                $this->_format = $this->_default_format;
            }
            if($this->_format === 'jsonp') {
                if(isset($_GET['callback']) && preg_match('/^[\w\.]+$/', $_GET['callback'])) {
                    $this->_jsonp_callback = $_GET['callback'];
                }
            }
        }
        try {
            $this->_auth_current_user();
        } catch(Exception $e) {
            $this->_forbidden($e->getMessage());
            $this->after();
            $this->request->send_headers();
            print $this->request->response;
            die();
        }
        return $pbefore;
    }

    public function after() {
        $this->request->response = $this->_serialize($this->response);
        $this->request->headers['Content-Type'] = 
            $this->_format_content_type($this->_format);
		$this->request->headers['Access-Control-Allow-Origin'] = '*';
        if($this->_cache_hit)
            $this->request->headers[self::HDR_CACHE_HIT] = 'true';
        return parent::after();
    }

    public function _auth_api_user() {
        $api_user = false;
        $servervals = array();
        foreach($_SERVER as $ki => $k) {
            if(preg_match('/^http_.+$/i', $ki))
                $servervals[strtolower(substr($ki, 5))] = $k;
        }
        $hdr_apikey = str_replace('-', '_', strtolower(self::HDR_API_KEY));
        if(isset($servervals[$hdr_apikey])) {
            $reqapikey = trim($servervals[$hdr_apikey]);
            $hdr_apitoken = str_replace('-', '_', strtolower(self::HDR_API_TOKEN));
            if(isset($servervals[$hdr_apitoken])) {
                $reqapitoken = trim($servervals[$hdr_apitoken]);
                $hdr_apidate = str_replace('-', '_', strtolower(self::HDR_API_DATE));
                if(isset($servervals[$hdr_apidate])) {
                    $reqdate = trim($servervals[$hdr_apidate]);
                    $reqtime = DateTime::createFromFormat('D, d M Y G:i:s O', $reqdate); // rfc2822
                    if($reqtime) $reqtime = $reqtime->getTimestamp();
                    else throw new Exception(sprintf('Invalid date header: "%s".', $reqdate));
                    $now = time();
                    if(abs($now - $reqtime) > self::API_DATE_MARGIN) {
                        throw new Exception(
                            sprintf('Date header value is out of bounds: %d (%d +/- %d).', $reqtime, time(), self::API_DATE_MARGIN)
                        );
                    }
                } else {
                    throw new Exception(sprintf('Missing "%s" header.', self::HDR_API_DATE));
                }
            } else {
                throw new Exception('Missing token header.');
            }
            $apikeym = ORM::factory('user_apikey')->where('apikey', '=', $reqapikey)->find();
            if($apikeym->loaded()) {
                $tgthash = md5(sprintf('%s-%s-%s', $reqdate, $apikeym->apikey, $apikeym->apisecret));
                if(!$tgthash === $reqapitoken) {
                    throw new Exception(sprintf('Invalid token "%s".', $reqapitoken));
                } else {
                    $api_user = ORM::factory('user', $apikeym->user_id);
                    $loginrole = ORM::factory('role')->where('name', '=', 'login')->find();
                    $apirole = ORM::factory('role')->where('name', '=', 'api')->find();
                    $adminrole = ORM::factory('role')->where('name', '=', 'admin')->find();
                    if(($api_user->has('roles', $loginrole) && $api_user->has('roles', $apirole)) ||
                        $api_user->has('roles', $adminrole)
                    ) {
                        // pass
                        $apikeym->requests = $apikeym->requests + 1;
                        $apikeym->save();
                    } else {
                        throw new Exception('You\'re not allowed to access the API. '.
                            'Contact an administrator if you have questions.'
                        );
                    }
                }
            } else {
                throw new Exception('Invalid API key.');
            }
        }
        return $api_user;
    }

    public function get_current_user() {
        return $this->_current_user;
    }

    public function _auth_current_user() {
        if($auth_user = Auth::instance()->get_user()) {
            $this->_current_user = $auth_user;
            $this->_api_user = false;
        } else {
            $api_user = $this->_auth_api_user();
            if($api_user) {
                $this->_current_user = $api_user;
                $this->_api_user = $api_user;
            } else {
                $this->_current_user = false;
                $this->_api_user = false;
            }
        }
        return $this->_current_user;
    }

    public function get_api_user() {
        $api_user = false;
        if($this->get_current_user())
            $api_user = $this->_api_user;
        return $api_user;
    }

    protected function _list_parameters() {
        $l = isset($_GET['l']) ? (int)$_GET['l'] : $this->_default_page_sz;
        $o = isset($_GET['o']) ? (int)$_GET['o'] : 0;
        $l = $l > $this->_max_page_sz || !$l ? $this->_max_page_sz : $l;
        return (object)array(
            'limit' => $l, 'offset' => $o
        );
    }

    protected function  _req_content_type() {
        $ct = '';
        if(in_array(strtolower(Request::$method), array('post', 'put'))) {
            $ct = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
            // ignore add'l params, charset, etc. for now.
            // TODO: stop ignoring.
            $ct = preg_replace('/;.+$/', '', $ct);
        }
        return $ct;
    }

    protected function  _serialization_formats() {
        $formats = array();
        $methods = get_class_methods(__CLASS__);
        foreach($methods as $i => $method) {
            if(preg_match('/^_serialize_(\w+)/', $method)) {
                $formats[] = str_replace('_serialize_', '', $method);
            }
        }
        return $formats;
    }

    protected function  _unserialization_formats() {
        $formats = array();
        $methods = get_class_methods(__CLASS__);
        foreach($methods as $i => $method) {
            if(preg_match('/^_unserialize_(\w+)/', $method)) {
                $formats[] = str_replace('_unserialize_', '', $method);
            }
        }
        return $formats;

    }

    protected function  _format_content_type($format=null) {
        $format = $format === null ? $this->_default_format : $format;
        if(isset($this->_content_types[$format])) {
            $content_type = $this->_content_types[$format];
        } else {
            $content_type = $this->_default_content_type;
        }
        return $content_type;
    }

    protected function _content_type_format($content_type=null) {
        $ct = $content_type === null ? $this->_default_content_type : $content_type;
        static $types;
        if(!$types) {
            $types = array();
            foreach($this->_content_types as $f => $t) {
                if(!is_array($t)) $t = array($t);
                foreach($t as $ti => $tv) $types[$tv] = $f;
            }
        }
        return isset($types[$ct]) ? $types[$ct] : $this->_default_format;
    }

    protected function  _serialize($data, $format=null) {
        static $formats = array();
        if(!$formats) $formats = $this->_serialization_formats();
        $format = $format === null ? $this->_format : $format;
        if(in_array($format, $formats)) {
            /*try {*/
                $serial = call_user_func(
                    array($this, '_serialize_'.$format), $data
                );
            /*} catch(Exception $e) {
                throw new Sourcemap_Exception_REST(
                    sprintf('Serialization error for format "%s".', $format)
                );
            }*/
        } else {
            throw new Sourcemap_Exception_REST(
                sprintf('Bad format "%s". (%s)', $format, join(',', $formats))
            );
        }
        return $serial;
    }

    protected function  _unserialize($str, $format=null) {
        static $formats = array();
        if(!$formats) $formats = $this->_unserialization_formats();
        $format = $format === null ? 
            $this->_content_type_format($this->_req_content_type()) : $format;
        if(in_array($format, $formats)) {
            try {
                $serial = call_user_func(
                    array($this, '_unserialize_'.$format), $str
                );
            } catch(Exception $e) {
                die($e);
                throw new Sourcemap_Exception_REST(
                    sprintf('Unserialization error for format "%s".', $format)
                );
            }
        } else {
            throw new Sourcemap_Exception_REST(
                sprintf('Bad format "%s". (%s)', $format, join(',', $formats))
            );
        }
        return $serial;

    }

    protected function  _serialize_php($data) {
        return serialize($data);
    }

    protected function  _serialize_json($data) {
        return json_encode($data);
    }

    protected function  _unserialize_json($str) {
        return json_decode($str);
    }

    protected function _serialize_form($data) {
        return http_build_query($data);
    }

    protected function _unserialize_form($str) {
        $data = null;
        parse_str($str, $data);
        if(!$data) $data = $_POST;
        return (object)array_merge($data, Sourcemap_Upload::get_uploads());
    }

    protected function _serialize_csv($data) {
        $csv = null;
        if(is_array($data)) {
            $csv = array();
            foreach($data as $dk => $dv) {
                if(!is_array($dv)) $dv = array($dv);
                $csv[] = Sourcemap_Csv::make_csv_row($dv);
            }
            $csv = implode("\n", $csv);
        } else throw new Exception('CSV serialization requires array.');
        return $csv;
    }

    protected function _unserialize_csv($csv) {
        return Sourcemap_Csv::parse($csv);
    }

    protected function  _serialize_jsonp($data, $callback=null) {
        $callback = $callback === null ? $this->_jsonp_callback : $callback;
        return sprintf('%s(%s);', $callback, $this->_serialize_json($data));
    }
	
	protected function  _serialize_geojson($data) {
		$supplychain = array_shift($data);
		return Sourcemap_Geojson::make($supplychain);				
    }
	
	protected function  _serialize_kml($data) {
		$supplychain = array_shift($data);
		return Sourcemap_Kml::make($supplychain);
    }	

	protected function  _serialize_xls($data) {
		$supplychain = array_shift($data);
		return Sourcemap_xls::make($supplychain);
    }
	
	protected function  _serialize_xlsx($data) {
		$supplychain = array_shift($data);
		return Sourcemap_xls::make($supplychain);
    }

    protected function _serialize_png($png) {
        return $png;
    }
    
    protected function _serialize_jpeg($jpeg) {
        return $jpeg;
    }

    protected function  _rest_error($code=400, $msg='Not found.') {
        $this->request->status = $code;
        $this->headers['Content-Type'] = $this->_format_content_type();
        $this->response = array(
            'error' => $msg
        );
    }

    protected function  _not_found($msg='Not found.') {
        return $this->_rest_error(404, $msg);
    }

    protected function _bad_request($msg='Bad request.') {
        return $this->_rest_error(400, $msg);
    }

    protected function _forbidden($msg='Forbidden.') {
        return $this->_rest_error(403, $msg);
    }

    protected function _internal_server_error($msg='Internal server error.') {
        return $this->_rest_error(500, $msg);
    }

    protected function cache_set($id, $data) {
        //pass
        // TODO: set cached data.
        return $this;
    }

    protected function cache_get($id, $default=null) {
        // TODO: get cached data if available.
        // $id = "sourcemap-services-".$id
        return false;
    }

}
