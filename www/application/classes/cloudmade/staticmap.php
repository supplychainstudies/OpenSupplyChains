<?php
class CloudMade_StaticMap {

    public static function get_base_url() {
        $api_key = Kohana::config('apis')->cloudmade_api_key;
        $url = sprintf('http://staticmaps.cloudmade.com/%s/staticmap', $api_key);
        return $url;
    }

    public static function get_url($raw_sc) {
        $markers = array();
        foreach($raw_sc->stops as $si => $stop) {
            $matches = array();
            if(preg_match('/^POINT\(((-|\+)?\d+(\.\d+)?) ((-|\+)?\d+(\.\d+)?)\)$/', $stop->geometry, $matches)) {
                $pt = new Sourcemap_Proj_Point($matches[1], $matches[4]);
                $pt = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $pt);
                $markers = array($pt->y, $pt->x);
            }
        }
        $qs = array();
        foreach($markers as $mi => $m) $qs[] = sprintf('marker=%f,%f', $m[0], $m[1]);
        $url = self::get_base_url().'?'.join($qs, '&');
        return $url;
    }

    public static function get_image($raw_sc) {
        $params = array('center' => "0,0", 'zoom' => 2, 'size' => '1024x600', 'styleid' => 1, 'format' => 'png32');
        $markers = array();
        $paths = array();
        foreach($raw_sc->stops as $i => $stop) {
            # todo: address hard limit of 100 stops.
            if($pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry)) {
                $pt = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $pt);
                $markers[] = 'size:small|opacity:0.9|label:'.($i+1).'|'.$pt->y.','.$pt->x;
                if($i > 0) {
                    $fromst = Sourcemap_Proj_Point::fromGeometry($raw_sc->stops[$i-1]->geometry);
                    $fromst = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $fromst);
                    $bentpts = self::make_bent_line($fromst, $pt);
                    $pts = array();
                    foreach($bentpts as $bpi => $bp) $pts[] = sprintf("%f,%f", $bp->y, $bp->x);
                    $paths[] = 'color:green|weight:3|opacity:.7|'.join('|', $pts);
                }
            }
        }
        foreach($raw_sc->hops as $i => $hop) {
            $geom = Sourcemap_Wkt::read($hop->geometry);
        }
        $ps = array();
        foreach($params as $k => $v) {
            $ps[] = "$k=$v";
        }
        foreach($markers as $i => $m) {
            $ps[] = 'marker='.$m;
        }
        foreach($paths as $i => $p) {
            $ps[] = 'path='.$p;
        }
        $ps = join($ps, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::get_base_url());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ps);
        return curl_exec($ch);
    }

    public static function make_bent_line($from, $to) {
        $r = 32;
        $pts = array();
        $dx = $to->x - $from->x;
        $dy = $to->y - $from->y;
        $maxdisp = sqrt($dx*$dx+$dy*$dy) * 0.05;
        if($dx == 0 || $dy == 0) {
            $pts[] = $from;
        } else {
            $theta = (pi()/2) - atan($dy/$dx);
            for($i=0; $i<$r; $i++) {
                $relamt = sin($i/$r*pi()) * $maxdisp;
                if(abs(sin($theta)) < abs(cos($theta))) {
                    $relamt = abs(sin(pi()*$dx/$dy));
                }
                $ddx = cos($theta+pi()) * $relamt;
                $ddy = sin($theta) * $relamt;
                $pts[] = new Sourcemap_Proj_Point($from->x + ($dx*$i/$r) + $ddx, $from->y + ($dy*$i/$r) + $ddy);
            }
        }
        $pts[] = $to;
        return $pts;
    }
}
