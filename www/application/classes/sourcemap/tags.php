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

class Sourcemap_Tags {
    
    const REGEX = '/^(\s+)?(\w+(\s+)?)*$/';

    public static function valid($tags) {
        $regex = self::REGEX;
        return preg_match($regex, $tags);
    }

    public static function parse($tags, $allow_dupes=false) {
        $p = array();
        if(self::valid($tags)) {
            $p = preg_split('/\s+/', $tags, null, PREG_SPLIT_NO_EMPTY);
            if($allow_dupes); //pass
            else {
                $pp = $p;
                $p = array();
                for($pi=0; $pi<count($pp); $pi++) {
                    $t = strtolower($pp[$pi]);
                    if(!in_array($t, $p))
                        $p[] = $t;
                }
            }
        }
        return $p;
    }

    public static function join($tags) {
        return join(' ', $tags);
    }
}
