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

class Kohana extends Kohana_Core {

    public static function include_path() {
        return self::$_paths;
    }

    public static function add_include_path($path) {
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(!in_array($path, self::$_paths)) {
            array_unshift(self::$_paths, $path);
        }
        return true;
    }

    public static function remove_include_path($path) {
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(in_array($path, self::$_paths)) {
            if(false !== ($i = array_search($path, self::$_paths))) {
                array_splice(self::$_paths, $i, 1);
            }
        }
        return true;
    }

}
