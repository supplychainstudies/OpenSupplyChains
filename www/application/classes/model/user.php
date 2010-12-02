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

    const FACTIVE = 1;
    #const FSUSPENDED = 64;
    #const FDELETED = 128;
    #const FVIP = 256;

    public function has_flag($flag) {
        $current_flags = (integer)$this->flags;
        $flag = (integer)$flag;
        return $current_flags & $flag;
    }

    public function has_flags() {
        $flags = 0;
        $args = func_get_args();
        foreach($args as $i => $arg) $flags |= (integer)$arg;
        return $this->has_flag($flags);
    }

    public function is_active() {
        return (bool)$this->has_flag(self::FACTIVE);
    }
}
