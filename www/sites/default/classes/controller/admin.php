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

class Controller_Admin extends Sourcemap_Controller_Layout {

    public $layout = 'admin';
    public $template = 'admin/dashboard';

    public function before() {
        $this->current_user = Auth::instance()->get_user();
        $admin = ORM::factory('role')
            ->where('name', '=', 'admin')->find();
        if($this->current_user && $this->current_user->has('roles', $admin)) {
            // pass
        } else {
            Message::instance()->set(
                'You\'re not allowed to access the management dashboard.', Message::ERROR
            );
            $this->request->redirect('auth');
        } 
        parent::before();
        $this->layout->page_title = 'Management';
    }
    
    public function action_index() {

        $supplychain = ORM::factory('supplychain');
        $user = ORM::factory('user');
        $usergroup = ORM::factory('usergroup');
        
        $today =  strtotime("now");
        $last_week = strtotime("-7 day");
        
        
        // last week's updates
        $get_supplychain = $supplychain->where('created', 'BETWEEN', array($last_week, $today))
            ->count_all();
        
        $get_userlogins = $user->where('last_login', 'BETWEEN', array($last_week, $today))
            ->count_all();
        
        $get_adminlogins = $user->where('username', '=', 'administrator')
            ->find()->as_array(null, 'last_login');
        $admin_lastlogin = date("F j \\a\\t g:i a", $get_adminlogins['last_login']);
        
        $total_count = $supplychain->count_all();
        $supplychain_array = $supplychain->find_all()->as_array();
        
        $get_usercreated = $user->where('created', 'BETWEEN', array($last_week, $today))
            ->count_all();
        
        
        //today's updates        
        $start_time = strtotime(strftime('%Y-%m-%d 00:00:00'));

        $get_usercreated_today = $user->where('created', 'BETWEEN', array($start_time, $today))
            ->count_all();
        
        $get_supplychaincreated_today = $supplychain->where('created', 'BETWEEN', array($start_time, $today))
            ->count_all();
        
        $stop =null;
        foreach ($supplychain_array as $supplychain_single) {
            $stop_count = $supplychain_single->stops->count_all();
            if ($stop_count > $stop) { 
            $stop = $stop_count;
                $supplychain_id = $supplychain_single->id;
            } else {
            $stop = $stop;
            }
        }

        $hop =null;
        foreach ($supplychain_array as $supplychain_single) {
            $hop_count = $supplychain_single->hops->count_all();
            if ($hop_count > $hop) { 
                $hop = $stop_count;
                $supplychain_hop_id = $supplychain_single->id;
            } else {
                $hop = $hop;
            }
        }
        
        $this->template->supplychain_lastweek = $get_supplychain;
        $this->template->user_lastlogin = $get_userlogins;
        $this->template->admin_lastlogin = $admin_lastlogin;
        $this->template->user_lastweek = $get_usercreated;
        $this->template->supplychain_id = $supplychain_id;
        $this->template->stop = $stop;

        $this->template->supplychain_hop_id = $supplychain_id;
        $this->template->hop = $hop;
        
        $this->template->user_today = $get_usercreated_today;
        $this->template->supplychain_today = $get_supplychaincreated_today;

        
        $this->layout->page_title = 'Sourcemap Management';
        Breadcrumbs::instance()->add('Management', 'admin/');
    }
    
  }
