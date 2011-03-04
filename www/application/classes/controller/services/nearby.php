<?php
class Controller_Services_Nearby extends Controller_Services {
    public function action_get() {
        $get = Validate::factory($_GET);
        $get->rule('latitude', 'numeric')
            ->rule('longitude', 'numeric')
            ->rule('placename', 'max_length', array(128))
            ->rule('placename', 'not_empty')
            ->rule('supplychain_id', 'numeric')
            ->rule('projection', 'regex', array('/epsg:[\w\d]+/i'))
            ->filter(true, 'trim');
        if($get->check()) {
            $get = $get->as_array();
            $proj = 'EPSG:4326'; // wgs84, by default
            if(isset($_GET['projection'])) {
                $proj = $get['projection'];
            }
            if(isset($_GET['latitude'], $_GET['longitude'])) {
                $pt = new Sourcemap_Proj_Point($get['latitude'], $get['longitude']);
            } elseif(isset($_GET['placename'])) {
                $results = Sourcemap_Geocoder::geocode($get['placename']);
                if($results) {
                    $r = $results[0];
                    $pt = new Sourcemap_Proj_Point($r->longitude, $r->latitude);
                } else {
                    return $this->_internal_server_error('Could not geocode placename.');
                }
            } else {
                return $this->_bad_request('Coordinates or placename required.');
            }
            $pt = Sourcemap_Proj::transform($proj, 'EPSG:900913', $pt);
        } else {
            return $this->_bad_request('Invalid parameters.');
        }
        $this->response = ORM::factory('stop')->nearby($pt);
    }
}
