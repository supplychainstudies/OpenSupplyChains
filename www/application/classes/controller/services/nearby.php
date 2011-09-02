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
