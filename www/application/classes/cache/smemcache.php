<?php
class Cache_Smemcache extends Kohana_Cache_Smemcache {

    public function prefix_key($k) {
        return sprintf('%s:%s', Sourcemap::$env, $k);
    }

    public function get($id, $default=null) {
        $id = $this->prefix_key($id);
        return parent::get($id, $default);
    }

    public function set($id, $data, $lifetime=3600) {
        $id = $this->prefix_key($id);
        return parent::set($id, $data, $lifetime);
    }
}
