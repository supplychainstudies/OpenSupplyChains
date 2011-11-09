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

class Sourcemap_Ip {
    
    public static function parse_dotip($dot_ip) {
        if(!preg_match('/(\d{1,3}\.){3}\d{1,3}/', $dot_ip))
            return false;
        $pts = explode('.', $dot_ip);
        foreach($pts as $i => $pt) {
            $pt = (int)$pt;
            if($pt < 0 || $pt > 255)
                return false;
            else
                $pts[$i] = $pt;
        }
        return $pts;
    }

    public static function dot2dec($dot_ip) {
        if(!($pts = self::parse_dotip($dot_ip)))
            return false;
        $pl = 0;
        $d = 0;
        while(count($pts)) {
            $pt = array_pop($pts);
            $d += $pt * pow(256, $pl++);
        }
        return $d;
    }

    public static function dec2dot($dec_ip) {
        $dec_ip = (int)$dec_ip;
        $d = array();
        $pl = 3;
        $cur = 0;
        do {
            $div = pow(256, $pl--);
            $r = $dec_ip % $div;
            $cur = ($dec_ip-$r) / $div; 
            $d[] = $cur;
            $dec_ip = $r;
        } while($pl>=0);
        return join('.', $d);
    }

    public static function find_ip($ip) {
        $results = Model::factory('iploc')->find($ip);
        for($i=0,$size = count($results); $i<$size; $i++) {
            $r = $results[$i];
            $results[$i]->placename = sprintf("%s, %s, %s",
                $r->city, $r->region, $r->country
            );
        }
        return $results;
    }
}
