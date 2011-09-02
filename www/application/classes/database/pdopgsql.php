<?php defined('SYSPATH') or die('No direct script access.');
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

class Database_PDOPGSQL extends Kohana_Database_PDOPGSQL {
    public function quote_table($value) {
        return parent::quote_table($value);
    }
    
    public function quote_identifier($value) {
        if(is_string($value)) {
            $value = parent::quote_identifier($value);
            $parts = explode('.', $value);
            foreach($parts as $i => $part) {
                if($part !== '*' && !preg_match('/^"[^"]+"$/', $part)) 
                    $parts[$i] = sprintf('"%s"', trim($part, '"'));
                else $parts[$i] = $part;
            }
            $value = join('.', $parts);
        } elseif(is_object($value) || is_array($value)) {
            $value = parent::quote_identifier($value);
        } 
        return $value;
    }

    public function get_connection() {
        return $this->_connection;
    }

    public function begin() {
        return $this->query(null, 'BEGIN', true);
    }

    public function commit() {
        return $this->query(null, 'COMMIT', true);
    }

    public function rollback() {
        return $this->query(null, 'ROLLBACK', true);
    }
}
