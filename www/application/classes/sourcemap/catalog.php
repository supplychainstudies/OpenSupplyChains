<?php
abstract class Sourcemap_Catalog {

    const CACHE_PREFIX = 'catalog-';

    public $long_name = 'Generic Catalog';
    public $short_name = 'generic';
    public $attribution = null;

    public $url = null;
    public $parameters = null;
    public $headers = null;

    protected $_cache = false;

    public function __construct($parameters=null) {
        $this->parameters = $parameters ? $parameters : null;
    }

    public function setup() {}

    public function teardown() {}

    public function fetch() {
        $response = Sourcemap_Http_Client::do_get(
            $this->url, $this->parameters, $this->headers
        );
        return $response->status_ok() ? $this->unserialize($response) : false;
    }

    public function unserialize($response) {
        $results = json_decode($response->body);
        $wrapped = array(
            'catalog' => array(
                'name' => array(
                    'long' => $this->long_name,
                    'short' => $this->short_name
                ),
                'attribution' => $this->attribution
            ),
            'parameters' => $this->parameters,
            'results' => $results
        );
        return $wrapped;
    }

    public function get_cache_key() {
        $pkeys = array_keys($this->parameters);
        sort($pkeys);
        $pts = array();
        for($i=0; $i<count($pkeys); $i++) {
            $pkey = $pkeys[$i];
            $pts[] = sprintf("%s:%s", $pkey, $this->parameters[$pkey]);
        }
        $cache_key = sprintf("%s%s", self::CACHE_PREFIX, join(':', $pts));
        return $cache_key;
    }

    public static function get($catalog, $parameters=null) {
        $cat = self::factory($catalog, $parameters);
        if(!$cat) return false;
        if($cat->_cache && ($cached = Cache::instance()->get($cat->get_cache_key()))) {
            $got = $cached;
        } else {
            $cat->setup();
            $got = $cat->fetch();
            if($cat->_cache) {
                Cache::instance()->set($cat->get_cache_key(), $got);
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
}
