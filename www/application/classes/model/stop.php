<?php
/**
 *  Stop model
 *
 */
class Model_Stop extends ORM {
    public $_table_names_plural = false;
    
    public $_has_many = array(
        'hops' => array(
            'model' => 'hop',
            'foreign_key' => 'from_stop_id'
        ),
        'attributes' => array(
            'model' => 'stop_attribute',
            'foreign_key' => 'stop_id'
        )
    );

    public $_belongs_to = array(
        'supplychain' => array(
            'foreign_key' => 'supplychain_id'
        )
    );

    protected function _select_columns() {
        return array(
            'id' => 'id', 'supplychain_id' => 'supplychain_id',
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
        if(!Sourcemap_Wkt::validate_geometry(Sourcemap_Wkt::POINT, $stop->geometry)) {
            throw new Exception('Bad stop: invalid WKT POINT geometry.');
        }
        if(!isset($stop->attributes) || !is_object($stop->attributes)) {
            throw new Exception('Bad stop: missing attributes.');
        }
        if($stop_ids !== false) {
            if(!isset($stop->id) || empty($stop->id) || in_array($stop->id, $stop_ids)) {
                throw new Exception('Bad stop: missing or duplicate id.');
            }
        }
        return true;
    }
}
