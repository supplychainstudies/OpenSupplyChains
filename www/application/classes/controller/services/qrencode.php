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

class Controller_Services_Qrencode extends Controller_Services {

    public $_format = 'png';
    public $_default_format = 'png';
    public $_default_content_type = 'image/png';
    public $_content_types = array(
        'png' => 'image/png'
    );

    protected function _serialize($data, $format=null) {
        return $data;
    }

    public function action_get() {
        $sz = isset($_GET['sz']) ? (int)$_GET['sz'] : null;
        if(isset($_GET['q'])) {
            $this->response = Sourcemap_QRencode::encode($_GET['q'], $sz);
        } else {
            return $this->_bad_request('Missing parameter "q".');
        }
    }
}
