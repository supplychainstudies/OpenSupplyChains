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

class Sourcemap_Database_Result_Cached extends Kohana_Database_Result_Cached {
    /**
     * Wraps Kohana database result as_array method for additional functionality.
     * @param string column to use as key in associative array
     * @param mixed column(s) to return as value(s)
     * @return array
     */
    public function as_array($key=null, $values=null) {
        $results = array();
        if(is_array($values)) {
            if($key !== null) {
                foreach($this as $i => $row) {
                    $results[$row->$key] = array();
                    foreach($values as $j => $value) {
                        $results[$row->$key][$value] = $row->$value;
                    }
                    $results[$row->$key] = (object)$results[$row->$key];
                }
            } else {
                foreach($this as $i => $row) {
                    $result = array();
                    foreach($values as $j => $value) {
                        $result[$value] = $row->$value;
                    }
                    $results[] = $result;
                }
            }
        } elseif($values === true) {
            foreach($this as $i => $row) {
                if($key !== null) {
                    $results[$row->$key] = (object)$row->as_array();
                } else {
                    $results[] = (object)$row->as_array();
                }
            }
        } else {
            $results = parent::as_array($key, $values);
        }
        return $results;
    }
}
