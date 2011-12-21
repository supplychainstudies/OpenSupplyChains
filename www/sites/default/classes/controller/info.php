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

class Controller_Info extends Sourcemap_Controller_Layout {
    public $layout = 'base';
    public $template = 'info/info';
    
    public function action_index() {}    
    public function action_api() { $this->template = View::factory('info/api'); }
    public function action_terms() { $this->template = View::factory('info/terms'); }
    public function action_dmca() { $this->template = View::factory('info/terms'); }
    public function action_privacy() { $this->template = View::factory('info/privacy'); }
    public function action_channel() { $this->template = View::factory('info/channel'); }
    public function action_payment() { $this->template = View::factory('info/payment'); }
    
}

