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
        $params = array('center' => "0,0", 'zoom' => 1, 'size' => '1024x600', 'styleid' => 1, 'format' => 'png32');
        $markers = array('size:small', 'opacity:0.9');
        foreach($raw_sc->stops as $i => $stop) {
            if($pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry)) {
                $pt = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $pt);
                $markers[] = $pt->y.','.$pt->x;
            }
        }
        $params['marker'] = join($markers, '|');
        $ps = array();
        foreach($params as $k => $v) {
            $ps[] = "$k=$v";
        }
        $ps = join($ps, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::get_base_url().'?'.$ps);
        #curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        #curl_setopt($ch, CURLOPT_POSTFIELDS, $ps);
        header('Content-Type: image/png');
        die(curl_exec($ch));
    }
}
