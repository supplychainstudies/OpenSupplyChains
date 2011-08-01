<?php
class Sourcemap_Form_Field_Textarea extends Sourcemap_Form_Field {
    protected $_type = 'textarea';

    protected function _makeInput() {
        $attr = $this->_html_attr;
        $attr['class'] = $this->css_class();
        return Form::textarea($this->name(),
            $this->value(), $attr
        );
    }

}
