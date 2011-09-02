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

class Sourcemap_User_Event_Registered extends Sourcemap_User_Event {

    protected $_tag = 'registered';

    public function __construct($user_id) {
        parent::__construct();
        $this->user_id = $user_id;
    }

    protected function get_recipients() {
        return array($this->user_id);
    }

    protected function get_data() {
        return array(
            'user_id' => $this->user_id
        );
    }

    public static function load($data) {
        if(isset($data['user_id'])) {
            $data['username'] = ORM::factory('user', $data['user_id'])->username;
        }
        return $data;
    }
}
