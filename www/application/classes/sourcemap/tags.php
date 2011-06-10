<?php
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
