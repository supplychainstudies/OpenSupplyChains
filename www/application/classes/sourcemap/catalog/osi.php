<?php
class Sourcemap_Catalog_Osi extends Sourcemap_Catalog {

    const DEFAULT_ACTION = 'search';

    public $action = 'search';

    public $long_name = 'footprinted dot org';
    public $short_name = 'osi';
    public $attribution = 'http://footprinted.org';

    public $url = 'http://footprinted.org/api/';

    public $_cache = true;
    public $_cache_ttl = 72000;

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
    }

    public function get_url() {
        return $this->url.$this->action.'/';
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
