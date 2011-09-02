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

class Sourcemap_Form_Field_Select extends Sourcemap_Form_Field {

    protected $_options = array();

    protected $_selected = null;
    protected $_type = Sourcemap_Form_Field::SELECT;

    protected function _makeInput() {
        $attr = $this->_html_attr;
        $attr['class'] = $this->css_class();
        return Form::select($this->name(), 
            $this->options(), $this->selected(), $attr
        );
    }

    public function value($value=null, $force=false) {
        return $this->selected($value);
    }

    public function selected($value=null) {
        if($value === null) {
            return $this->_selected;
        }
        if(isset($this->_options[$value]))
            $this->_selected = $value;
        return $this;
    }

    public function option($value, $label=null) {
        $this->_options[$value] = $label ? $label : $value;
        return $this;
    }

    public function drop_option($value) {
        if(isset($this->_options[$value]))
            unset($this->_options[$value]);
        return $this;
    }

    public function options($options=null) {
        if($options && is_array($options)) {
            $this->_options = $options;
            return $this;
        }
        return $this->_options;
    }
}
