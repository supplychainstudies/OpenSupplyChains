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

class Controller_User extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'user/profile';

    public function action_index($identifier=false) {
        // todo: cache this crap
        if(!$identifier) {
            Message::instance()->set('No user specified.');
            return $this->request->redirect('');
        }
        if(is_numeric($identifier)) {
            // pass
            $user = ORM::factory('user', $identifier);
        } else {
            $user = ORM::factory('user')->where('username', '=', $identifier)->find();
        }
        if($user->loaded()) {
            $user = (object)$user->as_array();
            unset($user->password);
            $user->avatar = Gravatar::avatar($user->email);
            unset($user->email);
            $this->template->user = $user;
            
            $pg = isset($_GET['p']) && (int)$_GET['p'] ? $_GET['p'] : 1;
            $pg = max($pg,1);

            $l = 10;
            $q = array(
                'user' => $user->id,
                'l' => $l, 'o' => ($pg-1)*$l,
                'p' => $pg, 'recent' => 'yes'
            );

            $r = Sourcemap_Search::find($q);

            $this->template->search_result = $r;
            
            $p = Pagination::factory(array(
                'current_page' => array(
                    'source' => 'query_string',
                    'key' => 'p'
                ),
                'total_items' => $r->hits_tot,
                'items_per_page' => $r->limit,
                'view' => 'pagination/basic'
            ));

            $this->template->pager = $p;

            $this->template->supplychains = $r->results;

        } else {
            Message::instance()->set('That user doesn\'t exist.');
            return $this->request->redirect('');
        }
    }
}
