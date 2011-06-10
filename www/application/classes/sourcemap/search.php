<?php
class Sourcemap_Search {

    const CACHE_PREFIX = 'search';

    const DBONLY = 0;
    const SPHINX = 1;

    public $default_limit = 25;
    public $max_limit = 100;
    public $min_limit = 1;


    public $search_type = 'simple';
    public $search_method = null;

    public $parameters = array();

    public $results = null;

    public $offset = 0;
    public $limit = 0;

    public $cache = false;
    public $cache_ttl = 60; // seconds


    public static function factory($p=null, $t=null) {
        //$cls = "Sourcemap_Search_$type";
        $cls = 'Sourcemap_Search_'.$t;
        $rc = new ReflectionClass($cls);
        return $rc->newInstance($p);
    }

    public static function available_searches() {
        $srchdir = dirname(__FILE__).'/search/';
        $dh = dir($srchdir);
        $searches = array();
        while(($f = $dh->read()) !== false) {
            if(preg_match('/^\w+\.php$/', $f)) {
                $srch = str_replace('.php', '', $f);
                $searches[$srch] = self::factory(ucfirst($srch))->get_metadata();
            }
        }
        return $searches;
    }

    public static function find($params=null, $type='simple') {
        return self::factory($params, $type)->search();
    }

    public static function simple($query) {
        return self::find(array('q' => $query), 'simple');
    }

    public static function cache_key(Sourcemap_Search $s) {
        return $s->get_cache_key();
    }

    public function get_cache_key() {
        $pkeys = is_array($this->parameters) ? array_keys($this->parameters) : array();
        sort($pkeys);
        $pts = array();
        for($i=0; $i<count($pkeys); $i++) {
            $pkey = $pkeys[$i];
            $pts[] = sprintf("%s:%s", $pkey, $this->parameters[$pkey]);
        }
        $cache_key = sprintf("%s-%s-%s", self::CACHE_PREFIX, $this->search_type, join(':', $pts));
        return $cache_key;

    }

    public static function cache(Sourcemap_Search $s) {
        $ckey = self::cache_key($s);
        Cache::instance()->set($ckey, $s->results, $s->cache_ttl);
    }

    public static function cache_load(Sourcemap_Search $s) {
        $ckey = self::cache_key($s);
        return Cache::instance()->get($ckey, null);
    }

    public function __construct($p=null) {
        if(isset($p['l'])) $this->set_limit($p['l']);
        else $this->set_limit($this->default_limit);
        if(isset($p['p'])) $this->set_page($p['p']);
        else {
            $this->offset = isset($p['o']) ? $p['o'] : 0;
            $this->offset = max(0, $this->offset);
        }
        $this->parameters = is_array($p) ? $p : array();
        $this->search_method = self::DBONLY;
    }

    public function __isset($k) {
        return isset($this->parameters[$k]);
    }

    public function __get($k) {
        if(!isset($this->parameters[$k])) return null;
        else return $this->parameters[$k];
    }

    public function __set($k, $v) {
        $this->parameters[$k] = $v;
        return;
    }
    
    public function get_metadata() {
        return array(
            'description' => 'Sourcemap search.'
        );
    }

    public function set_page($p) {
        $this->set_offset(($p-1) * $this->limit);
        return $this;
    }

    public function set_limit($l) {
        $this->limit = max(
            min($l, $this->max_limit), $this->min_limit
        );
        return $this;
    }

    public function set_offset($o) {
        $o = (int)$o;
        $this->offset = max(0, $o);
        return $this;
    }

    public function search() {
        if($this->cache && ($this->results = self::cache_load($this))) {
            $this->results->cache_hit = true;
        } else {
            $this->fetch();
            self::cache($this);
        }
        return $this->results;
    }

    public function fetch() {
        $this->results = Sourcemap_Search_Results::factory(
            $this->search_type, $this->parameters
        );
        return $this;
    }
}
