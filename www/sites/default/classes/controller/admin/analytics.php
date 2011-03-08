<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Admin_Analytics extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/analytics/details';


    public function action_index() {
	
	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    
	    $today =  strtotime("now");
	    $start_time = strtotime(strftime('%Y-%m-%d 00:00:00'));

	    $supplychain = ORM::factory('supplychain');
	    $user = ORM::factory('user');
	    $usergroup = ORM::factory('usergroup');
	    
	    $get_usercreated_today = $user->where('created', 'BETWEEN', array($start_time, $today))->find_all()->as_array('id', array('id', 'username', 'email'));	    
	    $get_supplychaincreated_today = $supplychain->where('created', 'BETWEEN', array($start_time, $today))->find_all()->as_array('id', array('id', 'user_id'));
	    $get_usergroupcreated_today = $usergroup->where('created', 'BETWEEN', array($start_time, $today))->find_all()->as_array('id', array('id', 'owner_id', 'name'));

	    $get_user_logins = $user->where('last_login', 'BETWEEN', array($start_time, $today))->find_all()->as_array('id', array('id', 'username', 'last_login'));

	    foreach($get_user_logins as $user_login) {
		$user_login->last_login =  date("h:i:s A", $user_login->last_login);
	    } 

	    $this->template->today_users = $get_usercreated_today;
	    $this->template->today_supplychains = $get_supplychaincreated_today;
	    $this->template->today_usergroups = $get_usergroupcreated_today;
	    $this->template->user_logins = $get_user_logins;

	    Breadcrumbs::instance()->add('Management', 'admin/')
		->add('Analytics', 'admin/analytics');
	}

    }
  }