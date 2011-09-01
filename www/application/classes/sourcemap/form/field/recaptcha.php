<?php    
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
