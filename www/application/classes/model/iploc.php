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

class Model_Iploc extends Model {
    public function find($ipaddr) {
        $decip = Sourcemap_Ip::dot2dec($ipaddr);
        $loc = false;
        if($decip) {
            $sql = sprintf(
                'select * from iploc_block where st <= %d and en >= %d', 
                $decip, $decip
            );
            $loc = $this->_db->query(Database::SELECT, $sql, true)->as_array();
        }
        return $loc;
    }
}
