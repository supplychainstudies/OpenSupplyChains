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
	
	public $envft_types = array(
		"co2e",
		"water",
		"energy",
		"waste",
	);
    
	public $_cache = true;
    public $_cache_ttl = 72000;

    //protected $_api_key = 'iftmf02b';

    public function __construct($params=null) {
		$this->_api_key = Kohana::config('apis')->footprinted_key;
		$this->osi_curated_catalog = Kohana::config('apis')->footprinted_list;
	
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

	public function cfetch() {		
		$count = 0;
		$count_start_offset = $this->parameters['offset'];
		$this->parameters['offset'] = 0;
		$this->parameters['limit'] = 10000;
		$response = parent::fetch();
		$whole_response = $response;
		unset($whole_response['results']);
        if($response) {
			$response = (object)$response;
			foreach ($response->results as $result) {
				foreach ($this->osi_curated_catalog as $fromlist) {
					if (strtolower($fromlist[0]) == strtolower($result->uri)) {
						if ($count >= $count_start_offset) {
							if (count($fromlist) > 1) {
								foreach ($fromlist as $order=>$part) {
									if ($order != 0) {
										$this->parameters['uri'] = $part;
										
										$part_response = parent::fetch();
										if (count($part_response['results']) != 0) {
											foreach($this->envft_types as $type) {
												if ($result->$type == null) {
													if ($part_response['results'][0]->$type != null) {
														$result->$type = $part_response['results'][0]->$type;
													}													
												}
											}
										}
									}
								}
							}
							$whole_response['results'][] = $result;
						}
						if ($count == ($count_start_offset+$this->limit-1)) {
							break 2;
						}
						$count++;
						break 1;
					}
				}
			}
			$whole_response = (object)$whole_response;
			unset($whole_response->parameters['limit']);
			unset($whole_response->parameters['offset']);
			$whole_response->parameters['l'] = $this->limit;
            $whole_response->parameters['o'] = $this->offset;
        	return $whole_response;
        }
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
