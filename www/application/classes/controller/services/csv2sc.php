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

class Controller_Services_Csv2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        if(is_object($posted) && isset($posted->stop_file) && $posted->stop_file instanceof Sourcemap_Upload) {
            $stop_csv = $posted->stop_file->get_contents();
            if(isset($posted->hop_file) && $posted->hop_file instanceof Sourcemap_Upload)
                $hop_csv = $posted->hop_file->get_contents;
        } elseif(($posted = (object)$_POST) && isset($posted->stop_csv)) {
            $stop_csv = $posted->stop_csv;
            if(isset($posted->hop_csv))
                $hop_csv = $posted->hop_csv;
        } else {
            return $this->_bad_request('No C.S.V. uploaded or posted.');
        }

        $sc = Sourcemap_Import_Csv::csv2sc($stop_csv, $hop_csv, (array)$posted);
        $this->response = (object)array('supplychain' => $sc);
    }
}
