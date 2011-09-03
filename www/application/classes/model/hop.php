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

class Model_Hop extends ORM {
    public $_table_names_plural = false;

    public $_belongs_to = array(
        'supplychain' => array(
            'foreign_key' => 'supplychain_id'
        )
    );

    protected function _select_columns() {
        return array(
            'id' => 'id',
            'from_stop_id' => 'from_stop_id', 'to_stop_id' => 'to_stop_id',
            'geometry' => array(DB::expr('ST_AsText(geometry)'), 'geometry')
        );
    }

    /**
     * Get saved.
     *
     * @return  ORM
     */
    public function save()
    {
    	if (array_key_exists('geometry', $this->_changed))
    	{
    		$this->_object['geometry'] = 
                DB::expr(sprintf('ST_SetSRID(ST_GeometryFromText(%s), %d)',
                    $this->_db->quote($this->_object['geometry']), Sourcemap::PROJ));
    	}

    	parent::save();
        $this->reload();
        return $this;
    }

    /**
     * Determines whether a raw data structure is hop-like.
     *
     * @param object $hop
     * @param array|boolean $stop_ids Stop identifiers to check against, false to skip checks.
     * @return boolean
     */
    public function validate_raw_hop($hop, $stop_ids=array()) {
        if(!$hop || empty($hop) || !is_object($hop)) {
            throw new Exception('Bad hop: empty or flat.');
        }
        if(!isset($hop->geometry)) {
            throw new Exception('Bad hop: missing geometry.');
        }
        /*if(!Sourcemap_Wkt::validate_geometry(Sourcemap_Wkt::MULTILINESTRING, $hop->geometry)) {
            throw new Exception('Bad stop: invalid WKT MULTILINESTRING geometry.');
        }*/
        if(!isset($hop->attributes) || !is_object($hop->attributes)) {
            throw new Exception('Bad hop: missing attributes.');
        }
        if($stop_ids !== false) {
            if(!isset($hop->from_stop_id) || !in_array($hop->from_stop_id, $stop_ids)) {
                throw new Exception('Bad hop: missing or invalid from stop.');
            }
            if(!isset($hop->to_stop_id) || !in_array($hop->to_stop_id, $stop_ids)) {
                throw new Exception('Bad hop: missing or invalid to stop.');
            }
        }
        return true;
    }
}
