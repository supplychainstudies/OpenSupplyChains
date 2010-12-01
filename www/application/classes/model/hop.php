<?php
/**
 *  Stop model
 *
 */
class Model_Hop extends ORM {
    public $_table_names_plural = false;
    public $_belongs_to = array(
        'supplychain' => array(
            'foreign_key' => 'supplychain_id'
        )
    );
    public $_has_one = array(
        'from_stop' => array(
            'model' => 'stop', 'foreign_key' => 'from_stop_id'
        ),
        'to_stop' => array(
            'model' => 'stop', 'foreign_key' => 'to_stop_id'
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
