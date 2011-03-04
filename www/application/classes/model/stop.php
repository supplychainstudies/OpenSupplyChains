<?php
/**
 *  Stop model
 *
 */
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
        return true;
    }

    public function nearby(Sourcemap_Proj_Point $pt, $supplychain_id=null, $limit=10) {
        $limit = min(max((int)$limit, 3), 25);
        $wkt = sprintf("POINT(%f %f)", $pt->x, $pt->y);
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
        return $q->execute()->as_array();
    }
}
