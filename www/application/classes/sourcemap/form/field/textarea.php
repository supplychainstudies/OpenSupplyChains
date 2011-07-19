<?php
class Sourcemap_Form_Field_Textarea extends Sourcemap_Form_Field {
    protected $_type = 'textarea';

    protected function _makeInput() {
        return Form::textarea($this->name(),
            $this->value(),
            array(
                'class' => $this->css_class()
            )
        );
    }

}
