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
    
        $today =  strtotime("now");
        $start_time = strtotime(strftime('%Y-%m-%d 00:00:00'));

        $supplychain = ORM::factory('supplychain');
        $user = ORM::factory('user');
        $usergroup = ORM::factory('usergroup');

        // todo: clean this up
        
        $usercreated_today = $user->where('created', 'BETWEEN', array($start_time, $today))
            ->find_all()->as_array('id', array('id', 'username', 'email'));

        $supplychaincreated_today = $supplychain->where('created', 'BETWEEN', array($start_time, $today))
            ->find_all()->as_array('id', array('id', 'user_id', 'created'));

        $today_supplychains = array();
        $scmodel = ORM::factory('supplychain');
        foreach($supplychaincreated_today as $scid => $sc) {
            $today_supplychains[] = $scmodel->kitchen_sink($scid);
        }

        $usergroupcreated_today = $usergroup->where('created', 'BETWEEN', array($start_time, $today))
            ->find_all()->as_array('id', array('id', 'owner_id', 'name'));

        $user_logins = $user->where('last_login', 'BETWEEN', array($start_time, $today))
            ->find_all()->as_array('id', array('id', 'username', 'last_login'));

        $this->template->today_users = $usercreated_today;
        $this->template->today_supplychains = $today_supplychains;
        $this->template->today_usergroups = $usergroupcreated_today;
        $this->template->user_logins = $user_logins;

        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Analytics', 'admin/analytics');
    }
}
