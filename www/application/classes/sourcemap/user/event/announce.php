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

class Sourcemap_User_Event_Announce extends Sourcemap_User_Event {

    protected $_tag = 'announce';

    public function __construct($message) {
        parent::__construct();
        $this->message = $message;
    }

    protected function get_recipients() {
        return array(array(null, Sourcemap_User_Event::EVERYBODY));
    }

    protected function get_data() {
        return array(
            'message' => $this->message
        );
    }
}
