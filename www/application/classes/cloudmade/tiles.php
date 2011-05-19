<?php
class Cloudmade_Tiles {

    const TILE_SZ = 65536;

    const TARGET_TILE_NUM = 1;
    const MAX_ZOOM = 18;
    const MIN_ZOOM = 1;

    const BASE_TILE_URL = 'http://tile.cloudmade.com/%s/%d/256/';
    const TILE_EXT = '.png';
    const TILE_STYLE = 5870;//5393;//31542;//22677;//11838;//4993;

    public static function get_tile_number($lat, $lon, $zoom=0) {
        $xtile = floor((($lon + 180) / 360) * pow(2, $zoom));
        $dlat = deg2rad($lat);
        $ytile = floor((1-log(tan($dlat)+1/cos($dlat))/pi())/2*pow(2,$zoom));
        return array($xtile, $ytile);
    }

    public static function get_tile_offset($lat, $lon, $zoom=0) {
        $xtile = (($lon + 180) / 360) * pow(2, $zoom);
        $xtile -= floor($xtile);
        $dlat = deg2rad($lat);
        $ytile = (1-log(tan($dlat)+1/cos($dlat))/pi())/2*pow(2,$zoom);
        $ytile -= floor($ytile);
        $tw = $th = sqrt(self::TILE_SZ);
        return array($xtile*256, $ytile*256);
    }

    public static function get_tile_nw($xtile, $ytile, $zoom) {
        $n = pow(2, $zoom);
        $lon = $xtile / $n * 360.0 - 180.0;
        $lat = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));
        return array($lat, $lon);
    }

    public static function get_tile_numbers($x0, $y0, $x1, $y1, $z=self::MAX_ZOOM) {
        if($z > self::MAX_ZOOM) throw new Exception('Zoom too high.');
        if($z < self::MIN_ZOOM) throw new Exception('Zoom too low.');
        $nw = self::get_tile_number($y0, $x0, $z);
        list($nwx, $nwy) = $nw;
        $se = self::get_tile_number($y1, $x1, $z);
        list($sex, $sey) = $se;
        $rows = ($sey - $nwy) + 1;
        $cols = ($sex - $nwx) + 1;
        $tiles = array();
        for($yi=$nwy; $yi<=$sey; $yi++) {
            $row = array();
            for($xi=$nwx; $xi<=$sex; $xi++) {
                $row[] = array($xi, $yi, $z);
            }
            $tiles[] = $row;
        }
        return $tiles;
    }

    public static function get_tiles_dim($x0, $y0, $x1, $y1, $z=self::MAX_ZOOM) {
        if($z > self::MAX_ZOOM) throw new Exception('Zoom too high.');
        if($z < self::MIN_ZOOM) throw new Exception('Zoom too low.');
        $nw = self::get_tile_number($y0, $x0, $z);
        list($nwx, $nwy) = $nw;
        $se = self::get_tile_number($y1, $x1, $z);
        list($sex, $sey) = $se;
        $rows = ($sey - $nwy) + 1;
        $cols = ($sex - $nwx) + 1;
        return array($cols, $rows);
    }

    public static function get_tileset_bounds($tileset) {
        $nw = $tileset[0][0];
        $s = $tileset[count($tileset)-1];
        $se = array($s[count($s)-1][0]+1, $s[count($s)-1][1]+1, $s[count($s)-1][2]);
        $nw = call_user_func_array(array('self', 'get_tile_nw'), $nw);
        $se = call_user_func_array(array('self', 'get_tile_nw'), $se);
        return array($nw[1], $nw[0], $se[1], $se[0]);
    }

    public static function get_tile_urls($numbers) {
        $ns = $numbers;
        $urls = array();
        foreach($ns as $yi => $row) {
            $new_row = array();
            foreach($row as $xi => $val) {
                list($x, $y, $z) = $val;
                $new_row[] = sprintf(self::BASE_TILE_URL, self::get_api_key(), self::TILE_STYLE)."$z/$x/$y".self::TILE_EXT; 
            }
            $urls[] = $new_row;
        }
        return $urls;
    }

    public static function get_sc_bbox($raw_sc) {
        $min_lat = $max_lat = $min_lon = $max_lon = null;
        foreach($raw_sc->stops as $i => $stop) {
            if($pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry)) {
                $pt = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $pt);
                if($min_lat === null || ($pt->y < $min_lat)) $min_lat = $pt->y;
                if($max_lat === null || ($pt->y > $max_lat)) $max_lat = $pt->y;
                if($min_lon === null || ($pt->x < $min_lon)) $min_lon = $pt->x;
                if($max_lon === null || ($pt->x > $max_lon)) $max_lon = $pt->x;
            }
        }
        $bbox = array($max_lat, $min_lon, $min_lat, $max_lon);
        return $bbox;
    }

    public static function fetch_tile($url) {
        $img = imagecreatefrompng($url);
        return $img;
    }

    public static function stitch_tiles($tiles, &$rtile_w=null, &$rtile_h=null) {
        $rows = count($tiles);
        $cols = count($tiles[0]);
        $tile_h = $tile_w = null;
        $stitched = null;
        for($y=0; $y<$rows; $y++) {
            for($x=0; $x<$cols; $x++) {
                $tile = self::fetch_tile($tiles[$y][$x]);
                if(!$tile_h) {
                    $tile_h = imagesy($tile);
                    $tile_w = imagesx($tile);
                }
                if(!$stitched) {
                    $stitched = imagecreatetruecolor($tile_w*$cols, $tile_h*$rows);
                    imagealphablending($stitched, true);
                    imageantialias($stitched, true);
                    imagesavealpha($stitched, true);
                }
                imagecopy($stitched, $tile, $tile_w*$x, $tile_h*$y, 0, 0, $tile_w, $tile_h);
            }
        }
        $rtile_w = $tile_w;
        $rtile_h = $tile_h;
        return $stitched;
    }

    public static function get_sc_tiles($scid) {
        if(!($raw_sc = ORM::factory('supplychain')->kitchen_sink($scid)))
            throw new Exception('Supplychain does not exist.');
        list($y0, $x0, $y1, $x1) = self::get_sc_bbox($raw_sc);
        return self::get_tile_urls($x0, $y0, $x1, $y1);
    }

    public static function get_sc_tiles_stitched($scid) {
        return self::stitch_tiles(self::get_sc_tiles($scid));
    }

    public static function get_api_key() {
        $api_key = Kohana::config('apis')->cloudmade_api_key;
        return $api_key;
    }
}
