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

class Controller_Admin_Analytics extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/analytics/details';


    public function action_index() {
    
        $now = time();
        $midnight = strtotime(strftime('%Y-%m-%d 00:00:00'));
        $weekago = strtotime("-1 week");
        $firstofmonth = strtotime(strftime('%Y-%m-1 00:00:00'));
        $sixmosago = strtotime(strftime('-6 months'));

        $supplychain = ORM::factory('supplychain');
        $user = ORM::factory('user');
        $usergroup = ORM::factory('usergroup');

        // todo: clean this up
        
        $today = array();
        $today['users'] = $user->where('created', 'BETWEEN', array($midnight, $now))
            ->count_all();
        $today['maps'] = $supplychain->where('created', 'BETWEEN', array($midnight, $now))
            ->count_all();
        $today['logins'] = $user->where('last_login', 'BETWEEN', array($midnight, $now))
            ->count_all();
        $this->template->today = (object)$today;

        $lastweek = array();
        $lastweek['users'] = $user->where('created', 'BETWEEN', array($weekago, $now))
            ->count_all();
        $lastweek['maps'] = $supplychain->where('created', 'BETWEEN', array($weekago, $now))
            ->count_all();
        $lastweek['logins'] = $user->where('last_login', 'BETWEEN', array($weekago, $now))
            ->count_all();
        $this->template->lastweek = (object)$lastweek;

        $thismonth = array();
        $thismonth['users'] = $user->where('created', 'BETWEEN', array($firstofmonth, $now))
            ->count_all();
        $thismonth['maps'] = $supplychain->where('created', 'BETWEEN', array($firstofmonth, $now))
            ->count_all();
        $thismonth['logins'] = $user->where('last_login', 'BETWEEN', array($firstofmonth, $now))
            ->count_all();
        $this->template->thismonth = (object)$thismonth;

        $sixmos = array();
        $sixmos['users'] = $user->where('created', 'BETWEEN', array($sixmosago, $now))
            ->count_all();
        $sixmos['maps'] = $supplychain->where('created', 'BETWEEN', array($sixmosago, $now))
            ->count_all();
        $sixmos['logins'] = $user->where('last_login', 'BETWEEN', array($sixmosago, $now))
            ->count_all();
        $this->template->sixmos = (object)$sixmos;


        $stop = $now;

        $week_maps = array();
        for($i=7; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'days' : 'day'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'days' : 'day'));
            $week_maps[] = $supplychain->where('created', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->week_maps = $week_maps;

        $fourweeks_maps = array();
        for($i=4; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'weeks' : 'week'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'weeks' : 'week'));
            $fourweeks_maps[] = $supplychain->where('created', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->fourweeks_maps = $fourweeks_maps;

        $sixmos_maps = array();
        for($i=6; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'months' : 'month'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'months' : 'month'));
            $sixmos_maps[] = $supplychain->where('created', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->sixmos_maps = $sixmos_maps;

        $week_users = array();
        for($i=7; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'days' : 'day'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'days' : 'day'));
            $week_users[] = $user->where('created', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->week_users = $week_users;

        $fourweeks_users = array();
        for($i=4; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'weeks' : 'week'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'weeks' : 'week'));
            $fourweeks_users[] = $user->where('created', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->fourweeks_users = $fourweeks_users;

        $sixmos_users = array();
        for($i=6; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'months' : 'month'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'months' : 'month'));
            $sixmos_users[] = $user->where('created', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->sixmos_users = $sixmos_users;

        $week_logins = array();
        for($i=7; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'days' : 'day'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'days' : 'day'));
            $week_logins[] = $user->where('last_login', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->week_logins = $week_logins;

        $fourweeks_logins = array();
        for($i=4; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'weeks' : 'week'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'weeks' : 'week'));
            $fourweeks_logins[] = $user->where('last_login', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->fourweeks_logins = $fourweeks_logins;

        $sixmos_logins = array();
        for($i=6; $i; $i--) {
            $start = strtotime(sprintf("-%d %s", $i, $i > 1 ? 'months' : 'month'));
            $stop = strtotime(sprintf("-%d %s", $i-1, $i > 1 ? 'months' : 'month'));
            $sixmos_logins[] = $user->where('last_login', 'BETWEEN', array($start, $stop))
                ->count_all();
        }

        $this->template->sixmos_logins = $sixmos_logins;



        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Analytics', 'admin/analytics');
    }
}
