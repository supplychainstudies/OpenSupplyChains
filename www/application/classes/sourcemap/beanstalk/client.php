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

class Sourcemap_Beanstalk_Client {

    protected $_conxn = null;
    protected $_host = '127.0.0.1';
    protected $_port = 11300;

    protected $_errors = null;

    protected $_persist = false;

    const MAXJOBSZ = 65536;

    public function __construct($host, $port) {
        $this->_host = $host;
        $this->_port = $port;
        $this->connect();
    }
    public function connect() {
        if(!$this->_persist || !$this->_conxn) $this->disconnect();
        if($this->_persist) {
            if($this->_conxn) {
                return true;
            } else {
                $this->_conxn = @pfsockopen($this->_host, $this->_port);
            }
        } else {
            $this->_conxn = @fsockopen($this->_host, $this->_port);
        }
        return $this->_conxn;
    }

    public function disconnect() {
        $this->_errors = array();
        unset($this->_conxn);
    }

    protected function _read() {
        return fread($this->_conxn, self::MAXJOBSZ);
    }

    protected function _readln() {
        return fgets($this->_conxn, self::MAXJOBSZ);
    }

    protected function _write($payload) {
        return fwrite($this->_conxn, $payload, self::MAXJOBSZ);
    }

    public function errors() {
        return $this->_errors;
    }
}
