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
        $params = array('size' => '1024x768', 'styleid' => 4993, 'format' => 'png32');
        $markers = array();
        $paths = array();
        $stop_dict = array();
        $min_lat = $max_lat = $min_lon = $max_lon = null;
        foreach($raw_sc->stops as $i => $stop) {
            # todo: address hard limit of 100 stops.
            if($pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry)) {
                $pt = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $pt);
                if($min_lat === null || ($pt->y < $min_lat)) $min_lat = $pt->y;
                if($max_lat === null || ($pt->y > $max_lat)) $max_lat = $pt->y;
                if($min_lon === null || ($pt->x < $min_lon)) $min_lon = $pt->x;
                if($max_lon === null || ($pt->x > $max_lon)) $max_lon = $pt->x;
                $color = '008000';
                if(isset($stop->attributes, $stop->attributes->color)) {
                    $stcolor = $stop->attributes->color;
                    if(preg_match('/^#?([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $stcolor))
                        $color = ltrim($stcolor, '#');
                }
                $sz = 16;
                if(isset($stop->attributes, $stop->attributes->size) && is_numeric($stop->attributes->size)) {
                    $sz = (int)$stop->attributes->size;
                    $sz += 16;
                }
                #$markerimg = "http://chart.apis.google.com/chart?cht=it&chs={$sz}x{$sz}&chco=$color&chx=ffffff,8&chf=bg,s,00000000&ext=.png";
                $markerimg = "http://chart.apis.google.com/chart?cht=it&chs={$sz}x{$sz}&chco=$color&chx=ffffff,8&chf=bg,s,00000000&ext=.png";
                $markers[] = 'url:'.urlencode($markerimg).'|opacity:0.85|'.$pt->y.','.$pt->x;
            }
            $stop_dict[$stop->local_stop_id] = $stop;
        }
        $bbox = array($min_lat, $min_lon, $max_lat, $max_lon);
        foreach($raw_sc->hops as $i => $hop) {
            $geom = Sourcemap_Wkt::read($hop->geometry);
            $fromst = Sourcemap_Proj_Point::fromGeometry($stop_dict[$hop->from_stop_id]->geometry);
            $fromst = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $fromst);
            $tost = Sourcemap_Proj_Point::fromGeometry($stop_dict[$hop->to_stop_id]->geometry);
            $tost = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $tost);
            $bentpts = self::make_bent_line($fromst, $tost);
            $pts = array();
            foreach($bentpts as $bpi => $bp) $pts[] = sprintf("%f,%f", $bp->y, $bp->x);
            $paths[] = 'color:green|weight:3|opacity:1|'.join('|', $pts);
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
        $ps[] = sprintf("bbox=%s", join(',', $bbox));
        //$ps[] = sprintf("center=%f,%f", ($max_lat - $min_lat)/2+$min_lat, ($max_lon-$min_lon)/2+$min_lon);
        //$ps[] = "zoom=3";
        $ps = join($ps, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::get_base_url());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ps);
        return curl_exec($ch);
    }

    public static function make_bent_line($from, $to) {
        $r = 8;
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
