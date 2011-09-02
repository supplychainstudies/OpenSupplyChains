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

class Sourcemap_Form_Field_Recaptcha extends Sourcemap_Form_Field {
    protected $_value = false;
    protected $_type = false;
 	protected $_name = false;   
	protected $_template = false; 
    
    public function __toString() { 
	  	

        // $attr = $this->_html_attr;
        // $attr['class'] = $this->css_class();
        // $attr['type'] = $this->field_type();
        // return Form::checkbox($this->name(), null, (bool)$this->value(), $attr);  
 		if (array_key_exists('recaptcha', Kohana::modules())) {
	    	 $recap = Recaptcha::instance();  
			return $recap->get_html();
		} else {
			return "";
		}
    }                           

}
