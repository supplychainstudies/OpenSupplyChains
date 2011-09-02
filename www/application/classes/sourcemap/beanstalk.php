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

class Sourcemap_Beanstalk {

    protected $_conxn = null;
    protected $_host = null;
    protected $_port = null;

    protected $_errors = null;

    public function __construct($host, $port) {
        $this->_host = $host;
        $this->_port = $port;
    }
    public function connect() {
        if($this->_conxn) $this->disconnect();
        // connect
    }

    public function disconnect() {
        // disconnect
    }

    public function errors() {
        return $this->_errors;
    }

    public function put() {
    
    }

    public function delete() {
    
    }

    public function uze($tube=null) {
    
    }
}
