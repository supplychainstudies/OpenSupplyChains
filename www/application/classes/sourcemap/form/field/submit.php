<?php
class Sourcemap_Form_Field_Submit extends Sourcemap_Form_Field {
    protected $_type = 'submit';

    public function label($l=null) {
        return $this->value($l);
    }
}
