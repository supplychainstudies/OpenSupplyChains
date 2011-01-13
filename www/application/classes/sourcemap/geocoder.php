<?php
class Sourcemap_Geocoder {
    
    const GOOGLE_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/';
    const GOOGLE_GEOCODE_FMT = 'json';

    public static function geocode($placename) {
        return self::get_google_results($placename);
    }

    public static function get_google_results($q) {
        $params = array('sensor' => 'false');
        if($q instanceof Sourcemap_Proj_Point) {
            $params['latlng'] = sprintf('%f,%f', $q->y, $q->x);
        } else {
            $params['address'] = $q;
        }
        $url = self::GOOGLE_GEOCODE_URL.self::GOOGLE_GEOCODE_FMT;
        $url .= '?'.http_build_query($params);
        if(!function_exists('curl_init'))
            throw new Exception('Curl is required for Sourcemap geocoding.');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result);
        $ret = array();
        if($result && isset($result->results)) {
            foreach($result->results as $i => $r) {
                die(print_r($r, true));
                $loc = $r->geometry->location;
                $loc->placename = $r->formatted_address;
                $ret[] = $loc;
            }
        }
        return $ret;
    }
}
