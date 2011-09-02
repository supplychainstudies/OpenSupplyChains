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

class Sourcemap_Http_Client { // cUrl library wrapper.

    public $url = null;
    public $method = null;
    protected $_ch = null;
    public $raw_response = null;
    public $user_agent = null;
    public $parameters = null;
    public $headers = null;

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const HEAD = 'HEAD';
    const TRACE = 'TRACE';
    const CONNECT = 'CONNECT';


    public function __construct($url=null) {
        $this->url = $url;
        $this->method = self::GET;
        $this->user_agent = sprintf('Sourcemap HTTP Client (%d)', Sourcemap::revision());
        $this->parameters = array();
        $this->headers = array();
        $this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
    }

    public function execute() {
        if(!$this->url) return false;
        $method = self::GET;
        $url = $this->url;
        switch($this->method) {
            case self::GET:
                $method = $this->method;
                $url .= $this->parameters ? '?'.http_build_query($this->parameters) : '';
                break;
            case self::POST:
            case self::PUT:
            case self::DELETE:
            case self::HEAD:
            case self::TRACE:
            case self::CONNECT:
                $method = $this->method;
                break;
            default:
                break;
        }
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->_ch, CURLOPT_URL, $url);
        $headers = array();
        foreach($this->headers as $hk => $hv) {
            $headers[] = sprintf("%s: %s", $hk, $hv);
        }
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $headers);
        $this->raw_response = curl_exec($this->_ch);
        if(!$this->raw_response) throw new Exception(curl_error($this->_ch));
        $this->response = Sourcemap_Http_Response::factory($this->raw_response);
        return $this->response;
    }

    public static function do_get($url, $parameters=null, $headers=null) {
        $client = new self($url);
        $client->parameters = $parameters ? $parameters : array();
        $client->headers = $headers ? $headers : array();
        try {
            $response = $client->execute();
        } catch(Exception $e) {
            throw new Exception('Could not fetch: '.$url.':'.$e);
        }
        return $response;
    }
}
