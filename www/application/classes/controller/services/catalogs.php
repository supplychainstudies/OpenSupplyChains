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

class Controller_Services_Catalogs extends Sourcemap_Controller_Service {
    public function action_get($catnm=null) {
        if(!$catnm) {
            $this->response = array(
                'catalogs' => Sourcemap_Catalog::available_catalogs()
            );
            return;
        }
        try {
            $results = Sourcemap_Catalog::get($catnm, $_GET);
        } catch(Exception $e) {
            return $this->_internal_server_error('Catalog is broken: '.$e);
        }
        $this->response = $results;
    }
}
