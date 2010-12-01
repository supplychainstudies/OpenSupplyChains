<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
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
