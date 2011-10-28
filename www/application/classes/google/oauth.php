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

class Google_Oauth {

    const SIGRSA = 'RSA-SHA1';
    const SIGHMAC = 'HMAC-SHA1';

    const OAUTH_BASE = 'https://www.google.com/accounts/';
    const OAUTH_REQTOKEN = 'OAuthGetRequestToken';
    const OAUTH_AUTHTOKEN = 'OAuthAuthorizeToken';
    const OAUTH_ACCTOKEN = 'OAuthGetAccessToken';

    const DEF_CKEY = 'anonymous';
    const DEF_CSECRET = 'anonymous';
    const DEF_DISPNM = 'Sourcemap';

    const SPREADSHEETS = 'https://spreadsheets.google.com/feeds/';


    public $_scope;
    public $_ckey;
    public $_csecret;
    public $_req_token_callback;

    public static function factory($scope) {
        $inst = new self($scope);
        return $inst;
    }


    public function __construct($scope) {
        $this->_scope =  $scope;
        $this->_ckey = self::DEF_CKEY;
        $this->_csecret = self::DEF_CSECRET;
        $this->_req_token_callback = Url::site('/oauth/reqtoken/', true);
    }

    public function get_acc_token($auth_token) {
        $headers = array(
            'Authorization' => $this->get_acc_token_auth_header($auth_token)
        );
        $url = self::OAUTH_BASE.self::OAUTH_ACCTOKEN;
        $response = Sourcemap_Http_Client::do_get($url, null, $headers);
        $resp_data = false;
        if($response->status_ok()) {
            parse_str($response->body, $resp_data);
        }
        return $resp_data;
    }

    public function get_req_token() {
        $headers = array(
            'Authorization' => $this->get_req_token_auth_header()
        );
        $url = self::OAUTH_BASE.self::OAUTH_REQTOKEN;
        $response = Sourcemap_Http_Client::do_get($url, null, $headers);
        $resp_data = false;
        if($response->status_ok()) {
            parse_str($response->body, $resp_data);
            $resp_data = $resp_data;
        }
        return $resp_data;
    }

    public function get_token_auth_header($auth_tok, $url) {
        $hdr = array();
        $params = $this->get_token_params($auth_tok);
        $params['oauth_signature'] = self::get_sig(
            $url, $params, self::SIGHMAC,
            $auth_tok['oauth_token_secret']
        );
        foreach($params as $k => $v) {
            $hdr[] = sprintf('%s="%s"', $k, urlencode($v));
        }
        return 'OAuth '.join(', ', $hdr);
    }

    public function get_acc_token_auth_header($auth_tok) {
        $hdr = array();
        $params = $this->get_acc_token_params($auth_tok);
        $params['oauth_signature'] = self::get_sig(
            self::OAUTH_BASE.self::OAUTH_ACCTOKEN, $params, self::SIGHMAC,
            $auth_tok['oauth_token_secret']
        );
        foreach($params as $k => $v) {
            $hdr[] = sprintf('%s="%s"', $k, urlencode($v));
        }
        return 'OAuth '.join(', ', $hdr);
    }

    public function get_req_token_auth_header() {
        $hdr = array();
        $params = $this->get_req_token_params();
        $params['oauth_signature'] = self::get_sig(
            self::OAUTH_BASE.self::OAUTH_REQTOKEN, $params
        );
        foreach($params as $k => $v) {
            $hdr[] = sprintf('%s="%s"', $k, urlencode($v));
        }
        return 'OAuth '.join(', ', $hdr);
    }

    public function get_token_params($auth_tok) {
        $params = $this->get_req_token_params();
        unset($params['oauth_callback']);
        unset($params['xoauth_displayname']);
        $params['oauth_token'] = $auth_tok['oauth_token'];
        return $params;
    }

    public function get_acc_token_params($auth_tok) {
        $params = $this->get_req_token_params();
        unset($params['oauth_callback']);
        unset($params['xoauth_displayname']);
        $params['oauth_token'] = $auth_tok['oauth_token'];
        $params['oauth_verifier'] = $auth_tok['oauth_verifier'];
        return $params;
    }

    public function get_req_token_params() {
        $params = array(
            'oauth_consumer_key' => $this->_ckey,
            'oauth_nonce' => $this->get_nonce(),
            'oauth_signature_method' => self::SIGHMAC,
            'oauth_timestamp' => time(),
            'scope' => $this->_scope,
            'oauth_callback' => $this->_req_token_callback,
            'oauth_version' => '1.0',
            'xoauth_displayname' => self::DEF_DISPNM
        );
        return $params;
    }

    public function get_nonce() {
        $nonce = dechex(rand(0, (1<<32)-1));
        $nonce .= dechex(rand(0, (1<<32)-1));
        $nonce .= dechex(rand(0, (1<<32)-1));
        $nonce .= dechex(rand(0, (1<<32)-1));
        return $nonce;
    }

    public static function get_sig($url, $params=array(), $type=self::SIGHMAC, $secret='', $method='GET') {
        $sig = false;
        $base_str = self::get_sig_base_str($url, $params, $method);
        if($secret) $secret = urlencode($secret);
        switch($type) {
            case self::SIGHMAC:
                $sig = base64_encode(hash_hmac('sha1', $base_str, self::DEF_CSECRET.'&'.$secret, true));
                break;
            case self::SIGRSA:
            default:
                throw new Exception('Unsupported signature type.');
                break;
        }
        return $sig;
    }

    public static function get_sig_base_str($url, $params, $method='GET') {
        $base_str = array();
        $base_str[] = strtoupper($method);
        $base_str[] = urlencode($url);
        if($norm = self::normalize_parameters($params))
            $base_str[] = $norm;
        return join('&', $base_str);
    }

    public static function normalize_parameters($params) {
        if(!$params) return '';
        $pkeys = array_keys($params);
        sort($pkeys);
        $norm = array();
        for($i=0,$size = count($pkeys); $i<$size; $i++) {
            $v = $params[$pkeys[$i]];
            if($pkeys[$i] === 'oauth_callback') $v = urlencode($v);
            elseif($pkeys[$i] === 'scope') $v = urlencode($v);
            elseif($pkeys[$i] === 'oauth_token') $v = urlencode($v);
            elseif($pkeys[$i] === 'oauth_verifier') $v = urlencode($v);
            $norm[] = sprintf("%s=%s", $pkeys[$i], $v);
        }
        return urlencode(join('&', $norm));
    }
}
