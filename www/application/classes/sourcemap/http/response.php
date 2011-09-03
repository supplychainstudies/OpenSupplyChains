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

class Sourcemap_Http_Response {

    public $status = null;
    public $headers = null;
    public $body = null;

    const OK = 200;
    const NOTFOUND = 404;
    const BADREQUEST = 400; // TODO: ...

    public static function factory($raw_resp) {
        $raw_resp = self::raw_split($raw_resp);
        if(!$raw_resp) return false;
        list($raw_headers, $body) = $raw_resp;
        $headers = self::raw_parse_headers($raw_headers);
        $resp = new Sourcemap_Http_Response($headers, $body);
        return $resp;
    }

    public static function raw_split($raw_resp) {
        $spl_at = strpos($raw_resp, "\r\n\r\n");
        if($spl_at === false) return $spl_at;
        $headers = substr($raw_resp, 0, $spl_at);
        $body = trim(substr($raw_resp, $spl_at));
        return array($headers, $body);
    }
    
    public static function raw_parse_headers($raw_headers) {
        $lines = explode("\n", $raw_headers);
        if(!$lines) return false;
        $status_line = trim(array_shift($lines));
        $m = null;
        if(!preg_match('/^HTTP\/(1\.0|1\.1)\s+(\d{3})\s+(.+)$/', $status_line, $m)) {
            return false;
        }
        list($status_line, $ver, $code, $msg) = $m;
        $headers = array();
        $headers['_status_code'] = $code;
        $headers['_status_msg'] = $msg;
        for($i=0; $i<count($lines); $i++) {
            $line = $lines[$i];
            if($parsed = self::raw_parse_header($line)) {
                list($k, $v) = $parsed;
                $headers[$k] = $v;
            }
        }
        return $headers;
    }

    public static function raw_parse_header($header) {
        $header = trim($header);
        $spl_at = strpos($header, ':');
        if($spl_at === false) return $spl_at;
        $k = str_replace('-', '_', strtolower(substr($header, 0, $spl_at)));
        $v = substr($header, $spl_at+1);
        return array($k, $v);
    }


    public function __construct($headers=null, $body=null) {
        $this->status = ($headers && isset($headers['_status_code'])) ? (int)$headers['_status_code'] : null;
        $this->headers = $headers ? $headers : array();
        $this->body = $body;
    }

    public function status_ok() {
        return $this->status === self::OK;
    }

    public function status_notfound() {
        return $this->status === self::NOTFOUND;
    }

    public function status_error() {
        return $this->status >= 400 && $this->status < 600;
    }
}
