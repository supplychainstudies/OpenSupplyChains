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

class Sourcemap_Catalog_Osi extends Sourcemap_Catalog {

    const DEFAULT_ACTION = 'search';

    public $action = 'search';

    public $long_name = 'footprinted dot org';
    public $short_name = 'osi';
    public $attribution = 'http://footprinted.org';

    public $url = 'http://footprinted.org/api/';

    public $_cache = true;
    public $_cache_ttl = 72000;

    protected $_api_key = 'iftmf02b';

    public function __construct($params=null) {
        $action = self::DEFAULT_ACTION;
        if(is_array($params)) {
            if(isset($params['action'])) {
                $action = $params['action'];
                unset($params['action']);
            }
        }
        $this->action = $action;
        parent::__construct($params);
        $this->parameters['limit'] = $this->limit;
        $this->parameters['offset'] = $this->offset;
        if(isset($this->parameters['q']))
            $this->parameters['name'] = $this->parameters['q'];
        $this->headers['Referer'] = Url::site('', true);
        $this->parameters['key'] = $this->_api_key;
    }

    public function get_url() {
        return $this->url.$this->action.'/';
    }

    public function fetch() {
        $response = parent::fetch();
        if($response) {
            $response = (object)$response;
            $response->parameters['l'] = $this->limit;
            $response->parameters['o'] = $this->offset;
        }
        return $response;
    }

    public function get_cache_key() {
        $ckey = parent::get_cache_key();
        $ckey .= '-'.$this->action;
        return $ckey;
    }

    public function get_metadata() {
        $meta = parent::get_metadata();
        $meta['options'] = array(
            'action' => array(
                'required' => 'false',
                'values' => array(
                    'search', 'category'
                ),
                'default' => 'search'
            )
        );
        return $meta;
    }
}
