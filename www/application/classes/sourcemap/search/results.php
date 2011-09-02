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

class Sourcemap_Search_Results implements Iterator {
    
    protected $_position;

    public $search_type = null;

    public $offset = 0;
    public $limit = 25;

    public $hits_tot = null; // all hits, if available
    public $hits_ret = 0; // should be less than $limit, hits returned.

    public $parameters = null;

    public $results = null;

    public $cache_hit = false;

    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return $this->results[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }

    public function valid() {
        return isset($this->results[$this->_position]);
    }

    public static function factory($st, $p=null) {
        $results = new Sourcemap_Search_Results($st);
        $results->parameters = $p;
        return $results;
    }

    public function __construct($st) {
        $this->search_type = $st;
    }
}
