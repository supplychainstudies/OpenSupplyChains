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

class Model_Stop extends ORM {
    public $_table_names_plural = false;
    
    public $_belongs_to = array(
        'supplychain' => array(
            'foreign_key' => 'supplychain_id'
        )
    );

    protected function _select_columns() {
        return array(
            'id' => 'id', 'supplychain_id' => 'supplychain_id', 
            'local_stop_id' => 'local_stop_id',
            'geometry' => array(DB::expr('ST_AsText(geometry)'), 'geometry')
        );
    }

    /**
     * Saves the current object. Will hash password if it was changed.
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
     * Determines whether a raw data structure is stop-like.
     *
     * @param object $stop
     * @param array|boolean $stop_ids Stop identifiers to check against, false to skip checks.
     * @return boolean
     */
    public function validate_raw_stop($stop, $stop_ids=array()) {
        if(!isset($stop->geometry)) {
            throw new Exception('Bad stop: missing geometry.');
        }
        /*if(!Sourcemap_Wkt::validate_geometry(Sourcemap_Wkt::POINT, $stop->geometry)) {
            throw new Exception('Bad stop: invalid WKT POINT geometry.');
        }*/
        if(!isset($stop->local_stop_id))
            throw new Exception('Bad stop: missing local id.');
        if(!isset($stop->attributes) || !is_object($stop->attributes)) {
            throw new Exception('Bad stop: missing attributes.');
        }
        if($stop_ids !== false) {
            if(!isset($stop->local_stop_id) || empty($stop->local_stop_id) || in_array($stop->local_stop_id, $stop_ids)) {
                throw new Exception('Bad stop: missing or duplicate id.');
            }
        }
        if(isset($stop->attributes->color)){
            $color = $stop->attributes->color;

            // make sure color has a # in front of it
            if(substr($color, 1) !== "#")
                $v = "#" . $v;

            $stop->attributes->color = $color;
        }
        return true;
    }

    public function nearby(Sourcemap_Proj_Point $pt, $limit=10, $supplychain_id=null) {
        if($supplychain_id === null && $this->loaded()) $supplychain_id = $this->id;
        $limit = min(max((int)$limit, 3), 25);
        $wkt = sprintf("POINT(%f %f)", $pt->x, $pt->y);
        if($supplychain_id) {
            $q = DB::query(Database::SELECT,
                "select supplychain_id, id as stop_id,
                    ST_Distance(ST_SETSRID(ST_GeometryFromText(:wkt), :proj), geometry) as dist
                from stop where supplychain_id = :scid order by dist asc limit :lim"
            );
            $q->parameters(array(
                ':wkt' => $wkt, ':proj' => Sourcemap::PROJ,
                ':lim' => $limit, ':scid' => $supplychain_id
            ));
        } else {
            $q = DB::query(Database::SELECT, 
                "select 
                    supplychain_id, id as stop_id,
                    ST_Distance(ST_SetSRID(ST_GeometryFromText(:wkt), :proj), geometry) as dist
                from stop order by dist asc limit :lim"
            );
            $q->parameters(array(
                ':wkt' => $wkt, ':proj' => Sourcemap::PROJ,
                ':lim' => $limit
            ));
        }
        return $q->execute()->as_array();
    }
}
