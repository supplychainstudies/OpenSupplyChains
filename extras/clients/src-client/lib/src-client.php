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
	public function supplychain($id) {
		return json_decode($this->_get(array('supplychains',$id)));
	}
	public function supplychains() {
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
