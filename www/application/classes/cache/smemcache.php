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

    public function delete($id, $timeout=0) {
        $id = $this->prefix_key($id);
        return parent::delete($id, $timeout);
    }
}
