<?php
class Sourcemap_Geocoder {
    
    const GOOGLE_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/';
    const GOOGLE_GEOCODE_FMT = 'json';

    public static function geocode($placename) {
        $ckey = sprintf("geocode-%s", md5($placename));
        if($cached = Cache::instance()->get($ckey)) {
            $results = $cached;
        } else {
            $results = self::get_google_results($placename);
            Cache::instance()->set($ckey, $results, 60 * 60 * 24 * 30);
        }
        return $results;
    }

    public static function get_google_results($q, $ttl=5) {
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
        if($result && isset($result->results) && !empty($result->results)) {
            foreach($result->results as $i => $r) {
                $loc = $r->geometry->location;
                $loc->placename = $r->formatted_address;
                $ret[] = $loc;
            }
        } else {
            error_log('sleeping and retrying: "'.$q.'" (ttl = '.$ttl.').');
            usleep((6 - $ttl) * 100000);
            return self::get_google_results($q, $ttl-1);
        }
        return $ret;
    }
}
