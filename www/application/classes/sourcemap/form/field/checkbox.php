<?php
class Sourcemap_Form_Field_Checkbox extends Sourcemap_Form_Field {
    protected $_value = false;
    protected $_type = 'checkbox';
    
    protected function _makeInput() {
        $attr = array(
            'class' => $this->css_class(),
            'type' => $this->field_type()
        );
        return Form::checkbox($this->name(), null, $this->value(), $attr);
    }

}
