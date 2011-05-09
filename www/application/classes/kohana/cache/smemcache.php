<?php
class Kohana_Cache_Smemcache extends Kohana_Cache_Memcache {
    public function _failed_request($host, $port) {
        return parent::_failed_request($host, $port);
    }
}
