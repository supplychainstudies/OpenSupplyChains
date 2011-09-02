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

class Sourcemap_Job {

    const STATICMAPGEN = 'staticmapgen';

    public $type = null;
    public $data = null;

    public function __toString() {
        return $this->get_seialized_data();
    }

    public static function factory($type, $data=null) {
        $cls = 'Sourcemap_Job_'.ucfirst($type);
        $rc = new ReflectionClass($cls);
        $inst = $rc->newInstance();
        $inst->type = $type;
        $inst->data = $data;
        return $inst;
    }

    public function get_serialized_data() {
        $json = json_encode(array(
            'type' => $this->type, 'data' => $this->data
        ));
        return $json;
    }

}
