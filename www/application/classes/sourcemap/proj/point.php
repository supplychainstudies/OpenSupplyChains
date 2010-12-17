<?php
class Sourcemap_Proj_Point {
    public function __construct($x, $y=null, $z=null) {
        if(is_array($x)) {
            if(isset($x[0], $x[1], $x[2])) {
                list($x, $y, $z) = $x;
            } else {
                throw new Error('Invalid coords.');
            }
        } elseif($y === null && $z === null && is_string($x)) {
            list($x, $y, $z) = self::parse_coords($coords);
        } else {
            $x = (float)$x;
            $y = (float)$y;
            $z = (float)$z;
        }
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function __toString() {
        return sprintf("x=%f,y=%f,z=%f", $this->x, $this->y);
    }

    public static function parse_coords($str) {
        $coords = split(',', $str);
        $ks = array('x', 'y', 'z');
        for($ci=0; $ci<count($ks); $ci++)
            if(isset($coords[$ci]) && preg_match('/^\s*'.$ks[$ci].'=\s*/', $coords[$ci]))
                $coords[$ci] = preg_replace('/^\s*'.$ks[$ci].'\s*=/', '', $coords[$ci]);
            $coords[$ci] = isset($coords[$ci]) ? (float)trim($coords[$ci]) : 0.0;
        return $coords;
    }
}
