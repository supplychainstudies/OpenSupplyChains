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

class Controller_Services_Xls2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        if(is_object($posted) && isset($posted->xls_file) && $posted->xls_file instanceof Sourcemap_Upload) {
            $xls = $posted->xls_file->get_contents();
        } elseif(($posted = (object)$_POST) && isset($posted->xls_file)) {
            $xls = $posted->xls_file;
        } else {
            return $this->_bad_request('No C.S.V. uploaded or posted.');
        }
        $sc = Sourcemap_Import_Xls::xls2sc($xls, (array)$posted);
        $this->response = (object)array('supplychain' => $sc);
    }
}
