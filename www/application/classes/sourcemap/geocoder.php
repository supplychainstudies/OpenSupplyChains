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

class Sourcemap_Geocoder {
    
    const GOOGLE_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/';
    const GOOGLE_GEOCODE_FMT = 'json';

    public static function geocode($placename) {
        // Detect whether the placename is a valid coordinate pair
        if (preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $placename, $matches)){
            $lat = (float)$matches[1];
            $lon = (float)$matches[3];
            if (($lat >= -90 && $lat <= 90) && ($lat >= -90 && $lat <= 90)){
                $results = array();
                $results[] = array(
                    // Using multiple keywords to match google's results
                    'lat' => $lat,
                    'lng' => $lon, 
                    'latitude' => $lat, 
                    'longitude' => $lon, 
                    'lon' => $lon, 
                    'placename' => $lat . "," . $lon
                );
                return $results;
            } else{
                //pass
            }
        } else{ 
            // pass 
        }

        // Not coordinates.  Let's let google handle this.
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
        if($ttl <= 0) return false;
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
                $loc->latitude = $loc->lat;
                $loc->longitude = $loc->lng;
                $loc->lon = $loc->lng;
                if(isset($r->address_components) && $r->address_components) {
                    $components = array();
                    foreach($r->address_components as $aci => $ac) {
                        $t = $ac->types;
                        if(in_array('political', $t) && in_array('country', $t)) {
                            $components['country'] = $ac->long_name;
                        } elseif(in_array('political', $t) && in_array('administrative_area_level_1', $t)) {
                            $components['province'] = $ac->long_name;
                        } elseif(in_array('political', $t) && in_array('administrative_area_level_2', $t)) {
                            $components['county'] = $ac->long_name;
                        } elseif(in_array('political', $t) && in_array('locality', $t)) {
                            $components['city'] = $ac->long_name;
                        }
                    }
                    foreach($components as $plk => $pln) $loc->$plk = $pln;
                }
				$loc->{'EPSG:900913'} = Sourcemap_Proj::transform(
					'WGS84', 'EPSG:900913', 
					new Sourcemap_Proj_Point($loc->lon, $loc->lat)
				);
				
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
