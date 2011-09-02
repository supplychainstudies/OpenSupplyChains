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

class Controller_Services_Geocode extends Controller_Services {
    public function action_get() {
        if(isset($_GET['placename']) && !empty($_GET['placename'])) {
            $q = $_GET['placename'];
        } elseif(isset($_GET['ll'])) {
            // note: parameter 'll' is lat/lng, as the WGS84 datum specifies.
            // should we do something to avoid confusion here? (e.g. lat=0&lng=0)?
            if(!preg_match('/((-|\+)?[\d]{1,3}(\.\d+)?,?){2}/', $_GET['ll']))
                return $this->_bad_request('Invalid lat/lng.');
            list($lat, $lng) = explode(',', $_GET['ll']);
            $q = new Sourcemap_Proj_Point($lng, $lat);
        } else {
            return $this->_bad_request('Placename or ll required.');
        }
        $this->response = (object)array(
            'results' => Sourcemap_Geocoder::geocode($q)
        );
    }
}
