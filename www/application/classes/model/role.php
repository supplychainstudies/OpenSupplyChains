<?php defined('SYSPATH') or die('No direct access allowed.');
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

class Model_Role extends Model_Auth_Role {

    public $_table_names_plural = false;

    // Relationships
    //protected $_has_many = array('user' => array('through' => 'user_role'));
    protected $_belongs_to = array('user' => array('through' => 'user_role'));

    // Validation rules
    protected $_rules = array(
    	'name' => array(
    		'not_empty'  => NULL,
    		'min_length' => array(4),
    		'max_length' => array(32),
    	),
    	'description' => array(
    		'max_length' => array(255),
    	),
    );


}
