<?php
/* Copyright (C) Sourcemap 2011
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. */

class SrcClient {
	
	const API_ENDPOINT = 'http://www.sourcemap.com/services/';
	const API_VERSION = '1.0';
	
	public $apikey;
	public $apisecret;
	
    public function __construct($key="", $secret="") {
		$this->apikey = $key;
		$this->apisecret = $secret;	
    }

    public function __toString() {
		return "Sourcemap Api Client Version:".self::API_VERSION;
	}
	
	public function available() {
		return json_decode($this->_get(array('')));
	}
	public function get_supplychain($id) {
		return json_decode($this->_get(array('supplychains',$id)));
	}	
	public function create_supplychain($data) {
		return $this->_post('supplychains', $data);
	}
	public function update_supplychain($infile, $id) {
		return $this->_put(array('supplychains',$id), $infile);
	}
	public function get_supplychains() {
		$args = func_get_args();
		if(isset($args[0]) && is_numeric($args[0])) {
			$l = $args[0];
			if(isset($args[1])) { $o = $args[1]; } else { $o = 0; }
			return json_decode($this->_get(array('supplychains','?l='.$l.'&o='.$o)));
		} else if(is_string($args[0])) {
			return json_decode($this->_get(array('search','?q='.$args[0])));
		} else { throw new Exception('Invalid Arguments'); }
	}
	
	/* Private functions */
	private function _get($service) {		
	    $ch = curl_init(self::API_ENDPOINT.join("/", $service));
	    $hdrs = $this->_sourcemap_api_auth_headers($this->apikey, $this->apisecret);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     return curl_exec($ch);
	}
	private function _post($service, $data=array()) {		
	    $ch = curl_init(self::API_ENDPOINT."/".$service);
	    $hdrs = $this->_sourcemap_api_auth_headers($this->apikey, $this->apisecret);
		$hdrs[] = "Content-Type: application/json";
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     return curl_exec($ch);
	}
	private function _put($service, $infile) {		
	    $ch = curl_init(self::API_ENDPOINT.join("/", $service));	
	    $hdrs = $this->_sourcemap_api_auth_headers($this->apikey, $this->apisecret);
		$hdrs[] = "Accept: application/json";
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
		curl_setopt($ch, CURLOPT_PUT, 1);
		curl_setopt($ch, CURLOPT_INFILE, fopen($infile, "r"));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	     return curl_exec($ch);
	}
	private function _sourcemap_api_auth_headers($apikey, $apisecret) {
	    $hdrs = array();
	    $hdrs[] = 'X-Sourcemap-API-Key: '.$apikey;
	    $date = $this->_sourcemap_api_auth_date();
	    $hdrs[] = 'Date: '.$date;
	    $apitoken = $this->_sourcemap_api_auth_token($apikey, $apisecret, $date);
	    $hdrs[] = 'X-Sourcemap-API-Token: '.$apitoken;
	    return $hdrs;
	}
	private function _sourcemap_api_auth_date($time=null) {
	    if(!$time) $time = time();
	    return date('r', $time);
	}
	private function _sourcemap_api_auth_token($apikey, $apisecret, $date) {
	    return md5(sprintf('%s-%s-%s', $date, $apikey, $apisecret));
	}
}
