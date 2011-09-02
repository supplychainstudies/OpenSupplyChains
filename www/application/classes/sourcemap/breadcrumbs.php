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

class Sourcemap_Breadcrumbs {

    public $_crumbs = array();
    public $_default_view = 'breadcrumbs';

    public function add($label, $uri=null, $class=null) {
        $this->_crumbs[] = (object)array(
            'label' => $label, 'uri' => $uri, 'class' => $class=null
        );
        return $this;
    }

    public function set($crumbs) {
        $this->_crumbs = array();
        foreach($crumbs as $i => $crumb) {
            call_user_func_array(array($this, 'add'), $crumb);
        }
        return $this;
    }

    public function get() {
        return $this->_crumbs;
    }

    public function render($view=null) {
        $view = $view ? $view : $this->_default_view;
        $view = View::factory($view);
        $view->set('breadcrumbs', $this->_crumbs);
        return $view->render();
    }
}
