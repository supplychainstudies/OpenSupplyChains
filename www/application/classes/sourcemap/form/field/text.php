<?php
class Sourcemap_Form_Field_Text extends Sourcemap_Form_Field {
    
    protected $_css_class = "textbox";

    protected function _makeInput() {
        return Form::select($this->name(),
            $this->options(), $this->selected(),
            array(
                'class' => $this->css_class()
            )
        );
    }

}
