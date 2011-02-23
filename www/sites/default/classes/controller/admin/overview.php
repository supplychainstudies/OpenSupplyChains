<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Admin_Overview extends Controller_Admin {
    
    public $layout = 'admin';
    public $template = 'admin/overview';
    
    public function action_index() {
	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $supplychain = ORM::factory('supplychain');
	    $user = ORM::factory('user');
	    $today =  strtotime("now");
	    $last_week = strtotime("-7 day");

	    $get_supplychain = $supplychain->where('created', 'BETWEEN', array($last_week, $today))
		->find()->count_all();

	    $get_userlogins = $user->where('last_login', 'BETWEEN', array($last_week, $today))
		->find()->count_all();

	    $get_adminlogins = $user->where('username', '=', 'administrator')
		->find()->as_array(null, 'last_login');
	    $admin_lastlogin = date("F j, Y, g:i a", $get_adminlogins['last_login']);
	    
	    
	    $this->template->supplychain_lastweek = $get_supplychain;
	    $this->template->user_lastweek = $get_userlogins;
	    $this->template->admin_lastlogin = $admin_lastlogin;
	    Breadcrumbs::instance()->add('Management', 'admin/')
		->add('Overview', 'admin/overview');
	}
    }
  }
 