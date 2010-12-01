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
                DB::expr(sprintf('ST_SetSRID(ST_GeometryFromText(%s), 3785)',
                    $this->_db->quote($this->_object['geometry'])));
		}

		parent::save();
        $this->reload();
        return $this;
	}
}
