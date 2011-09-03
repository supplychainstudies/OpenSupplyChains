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

class Sourcemap_ORM extends Kohana_ORM {

    public $_table_names_plural = false;

    protected $_select_columns = array();

    protected $_get_currval = true;

    public function get_db() {
        return $this->_db;
    }

    public function sequence_name() {
        return $this->_table_name.'_'.$this->_primary_key.'_seq';
    }

    /**
     * Tests if this object has a relationship to a different model.
     *
     * @param   string   alias of the has_many "through" relationship
     * @param   ORM      related ORM model
     * @return  boolean
     */
    public function has($alias, $model) {
    	// Return count of matches as boolean
    	return (bool) DB::select(array(DB::expr('COUNT(*)'), 'records_found'))
    		->from($this->_has_many[$alias]['through'])
    		->where($this->_has_many[$alias]['foreign_key'], '=', $this->pk())
    		->where($this->_has_many[$alias]['far_key'], '=', $model->pk())
    		->execute($this->_db)
    		->get('records_found');
    }
    
    /**
     * Loads a database result, either as a new object for this model, or as
     * an iterator for multiple rows.
     *
     * @chainable
     * @param   boolean       return an iterator or load a single row
     * @return  ORM           for single rows
     * @return  ORM_Iterator  for multiple rows
     */
    protected function _load_result($multiple = FALSE) {
    	$this->_db_builder->from($this->_table_name);

    	if ($multiple === FALSE)
    	{
    		// Only fetch 1 record
    		$this->_db_builder->limit(1);
    	}

        
    	// Select all columns by default
        $cols = in_array('_select_columns', get_class_methods($this)) && is_array($this->_select_columns()) ? 
            array_values($this->_select_columns()) : $this->_table_name.'.*';
        if(is_array($cols)) {
            $this->_db_builder->select_array($cols);
        } else {
            $this->_db_builder->select($cols);
        }

    	if ( ! isset($this->_db_applied['order_by']) AND ! empty($this->_sorting))
    	{
    		foreach ($this->_sorting as $column => $direction)
    		{
    			if (strpos($column, '.') === FALSE)
    			{
    				// Sorting column for use in JOINs
    				$column = $this->_table_name.'.'.$column;
    			}

    			$this->_db_builder->order_by($column, $direction);
    		}
    	}

    	if ($multiple === TRUE)
    	{
    		// Return database iterator casting to this object type
    		$result = $this->_db_builder->as_object(get_class($this))->execute($this->_db);

    		$this->reset();

    		return $result;
    	}
    	else
    	{
    		// Load the result as an associative array
    		$result = $this->_db_builder->as_assoc()->execute($this->_db);

    		$this->reset();

    		if ($result->count() === 1)
    		{
    			// Load object values
    			$this->_load_values($result->current());
    		}
    		else
    		{
    			// Clear the object, nothing was found
    			$this->clear();
    		}

    		return $this;
    	}
    }

    /**
     * Count the number of records in the table. Fixes 
     * problem with Kohana_ORM count_all method using
     * DB::expr around count(*) sql.
     *
     * @return  integer
     */
    public function count_all() {
    	$selects = array();

    	foreach ($this->_db_pending as $key => $method)
    	{
    		if ($method['name'] == 'select')
    		{
    			// Ignore any selected columns for now
    			$selects[] = $method;
    			unset($this->_db_pending[$key]);
    		}
    	}

    	$this->_build(Database::SELECT);

    	$records = (int) $this->_db_builder->from($this->_table_name)
    		->select(array(DB::expr('COUNT(*)'), 'records_found'))
    		->execute($this->_db)
    		->get('records_found');

    	// Add back in selected columns
    	$this->_db_pending += $selects;

    	$this->reset();

    	// Return the total number of records in a table
    	return $records;
    }

    public function save() {
        if(($this->empty_pk() || isset($this->_changed[$this->_primary_key])) && $this->_get_currval) {
            $connection = $this->_db->get_connection();
            $connection->beginTransaction();
            try {
                parent::save();
                $result = $this->_db->query(Database::SELECT, 
                    'select currval(\''.$this->sequence_name().'\') '.
                    'as last_insert_id', 
                    false
                );
                $result = $result->as_array();
                $last_insert_id = $result[0]['last_insert_id'];
                $this->_object[$this->_primary_key] = $last_insert_id;
            } catch(Exception $e) {
                try {
                    $connection->rollBack();
                } catch(Exception $ee) {}
                throw $e;
            }
            $connection->commit();
        } else {
            return parent::save();
        }
    }

}

