<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Model_User extends Model_Auth_User {

    public $_table_names_plural = false;

	protected $_has_many = array(
		'user_tokens' => array('model' => 'user_token'),
		'roles' => array(
            'model' => 'role', 'through' => 'user_role', 
        ),
        'groups' => array(
            'model' => 'usergroup', 'through' => 'user_usergroup'
        )
	);
}
