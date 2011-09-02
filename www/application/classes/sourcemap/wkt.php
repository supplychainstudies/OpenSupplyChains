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

class Sourcemap_Wkt {

    const ANY = -1;
    const POINT = 'point';
    const LINESTRING = 'linestring';
    const POLYGON = 'polygon';
    const MULTIPOINT = 'multipoint';
    const MULTILINESTRING = 'multilinestring';
    const MULTIPOLYGON = 'multipolygon';
    const GEOMETRYCOLLECTION = 'geometrycollection';

    const RE_TYPE = '/^\s*(\w+)\s*\(\s*(.*)\s*\)\s*$/';
    const RE_SPAC = '/\s+/';
    const RE_PCOM = '/\)\s*,\s*\(/';
    const RE_TRMP = '/^\s*\(?(.*?)\)?\s*$/';

    public static function read($str) {
        $matches = null;
        preg_match(self::RE_TYPE, $str, $matches);
        if($matches) {
            $type = strtolower($matches[1]);
            $gstr = $matches[2];
            $geom = self::parse($type, $gstr);
        }
        return $geom;
    }

    public static function raw_write($type) {
        $args = func_get_args();
        array_shift($args);
        $wkt = false;
        switch($type) {
            case self::POINT:
                if(count($args) !== 1)
                    throw new Exception('Exactly one argument allowed for type POINT.');
                elseif(!($args[0] instanceof Sourcemap_Proj_Point))
                    throw new Exception('Sourcemap_Proj_Point expected.');
                $pt = $args[0];
                $wkt = sprintf("%f %f", $pt->x, $pt->y);
                break;
            case self::LINESTRING:
                if(count($args) < 2)
                    throw new Exception('At least two points required for LINESTRING.');
                $pts = array();
                foreach($args as $i => $a) {
                    $pts[] = self::raw_write(self::POINT, $a);
                }
                $wkt = join(', ', $pts);
                break;
            case self::MULTILINESTRING:
                if(count($args) < 1)
                    throw new Exception('At least one linestring expected.');
                $linestrings = array();
                foreach($args as $i => $a) {
                    if(!is_array($a))
                        throw new Exception('Array of points expected.');
                    $aargs = $a;
                    array_unshift($aargs, self::LINESTRING);
                    $linestring = call_user_func_array(array('self', 'raw_write'), $aargs);
                    $linestrings[] = sprintf('(%s)', $linestring);
                }
                $wkt = join(', ', $linestrings);
                break;
            default:
                throw new Exception('Wkt type "'.$type.'" not implemented.');
                break;
        }
        return $wkt;
    }

    public static function write($type) {
        $args = func_get_args();
        $wkt = false;
        switch($type) {
            case self::POINT:
                $raw = call_user_func_array(array('self', 'raw_write'), $args);
                $wkt = sprintf("POINT(%s)", $raw);
                break;
            case self::LINESTRING:
                $raw = call_user_func_array(array('self', 'raw_write'), $args);
                $wkt = sprintf("LINESTRING(%s)", $raw);
                break;
            case self::MULTILINESTRING:
                $raw = call_user_func_array(array('self', 'raw_write'), $args);
                $wkt = sprintf("MULTILINESTRING(%s)", $raw);
                break;
            default:
                $wkt = call_user_func_array(array('self', 'raw_write'), $args);
                break;
        }
        return $wkt;
    }

    public static function parse($type, $gstr) {
        $geom = null;
        switch($type) {
            case self::POINT:
                $matches = null;
                $geom = preg_split(self::RE_SPAC, trim($gstr), $matches);
                break;
            case self::LINESTRING:
                $pts = explode(',', trim($gstr));
                $points = array();
                foreach($pts as $i => $pt) {
                    $points[] = self::parse(self::POINT, $pt);
                }
                $geom = $points;
                break;
            case self::MULTILINESTRING:
                $lines = preg_split(self::RE_PCOM, trim($gstr));
                $components = array();
                foreach($lines as $i => $line) {
                    $line = preg_replace(self::RE_TRMP, '$1', $gstr);
                    $components[] = self::parse(self::LINESTRING, $line);
                }
                $geom = $components;
                break;
            case self::POLYGON:
            case self::MULTIPOINT:
            case self::MULTIPOLYGON:
            case self::GEOMETRYCOLLECTION:
            case self::ANY:
            default:
                break;
        }
        return array($type, $geom);
    }
    

}
