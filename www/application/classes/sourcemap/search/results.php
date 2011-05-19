<?php
class Sourcemap_Search_Results {
    public $search_type = null;

    public $offset = 0;
    public $limit = 25;

    public $hits_tot = null; // all hits, if available
    public $hits_ret = 0; // should be less than $limit, hits returned.

    public $parameters = null;

    public $results = null;

    public $cache_hit = false;

    public static function factory($st, $p=null) {
        $results = new Sourcemap_Search_Results($st);
        $results->parameters = $p;
        return $results;
    }

    public function __construct($st) {
        $this->search_type = $st;
    }
}
