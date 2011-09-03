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

class Gravatar {

    public static $_avatar_base = 'http://www.gravatar.com/avatar/%s?d=%s&s=%d'; // identicon | retro

    public static function avatar($email, $sz=64, $d=null) {
    	if($d === null) { 
            $d = URL::base(true, true)."assets/images/default-user.png"; 
        }
        $sz = min(512, max(1, $sz), $sz);
        return sprintf(self::$_avatar_base, self::hash($email), urlencode($d), $sz);
    }

    public static function hash($email) {
        return md5(strtolower(trim($email)));
    }
}
