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

abstract class Sourcemap_Catalog {

    const CACHE_PREFIX = 'catalog-';

    public $long_name = 'Generic Catalog';
    public $short_name = 'generic';
    public $attribution = null;

    public $url = null;
    public $parameters = null;
    public $headers = null;

    public $_cache = false;
    public $_cache_ttl = 2592000; // seconds

    public $minlimit = 1;
    public $maxlimit = 25;

    public function __construct($parameters=null) {
        $this->parameters = $parameters ? $parameters : null;
        $this->limit = isset($parameters['l']) ? 
            min(max($this->minlimit, (int)$parameters['l']), $this->maxlimit) : 
            $this->maxlimit;
        $this->offset = isset($parameters['o']) ?   
            (int)$parameters['o'] : 0;
        if(isset($parameters['l'])) unset($this->parameters['l']);
        if(isset($parameters['o'])) unset($this->parameters['o']);
    }

    public function setup() {}

    public function teardown() {}

    public function get_url() {
        return $this->url;
    }

    public function fetch() {
        $response = Sourcemap_Http_Client::do_get(
            $this->get_url(), $this->parameters, $this->headers
        );
        return $response->status_ok() ? $this->unserialize($response) : false;
    }

    public function unserialize($response) {
        $results = json_decode($response->body);
        $wrapped = array(
            'catalog' => $this->get_metadata(),
            'parameters' => $this->parameters,
            'results' => $results
        );
        return $wrapped;
    }

    public function get_cache_key() {
        $pkeys = is_array($this->parameters) ? array_keys($this->parameters) : array();
        sort($pkeys);
        $pts = array();
        for($i=0,$size = count($pkeys); $i<$size; $i++) {
            $pkey = $pkeys[$i];
            $pts[] = sprintf("%s:%s", $pkey, $this->parameters[$pkey]);
        }
        $cache_key = sprintf("%s%s", self::CACHE_PREFIX, join(':', $pts));
        return $cache_key;
    }

    public function get_metadata() {
        return array(
            'name' => array(
                'long' => $this->long_name,
                'short' => $this->short_name
            ),
            'attribution' => $this->attribution
        );
    }

    public static function get($catalog, $parameters=null) {
        $cat = self::factory($catalog, $parameters);
        if(!$cat) return false;
        if($cat->_cache && ($cached = Cache::instance()->get($cat->get_cache_key()))) {
            $got = $cached;
            if($got && is_array($got)) $got['cache_hit'] = true;
        } else {
            $cat->setup();
            $got = $cat->fetch();
            if($cat->_cache) {
                Cache::instance()->set($cat->get_cache_key(), $got, $cat->_cache_ttl);
            }
            $cat->teardown();
        }
        return $got;
    }

    public static function factory($catalog, $parameters=null) {
        $cls = "Sourcemap_Catalog_$catalog";
        $rc = new ReflectionClass($cls);
        return $rc->newInstance($parameters);
    }

    public static function available_catalogs() {
        $catdir = dirname(__FILE__).'/catalog/';
        $dh = dir($catdir);
        $catalogs = array();
        while(($f = $dh->read()) !== false) {
            if(preg_match('/^\w+\.php$/', $f)) {
                $cat = str_replace('.php', '', $f);
                $catalogs[$cat] = self::factory(ucfirst($cat))->get_metadata();
            }
        }
        return $catalogs;
    }
}
