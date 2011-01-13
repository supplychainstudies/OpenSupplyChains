<?php
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
