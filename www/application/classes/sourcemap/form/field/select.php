<?php
class Sourcemap_Form_Field_Select extends Sourcemap_Form_Field {

    protected $_options = array();

    protected $_selected = null;
    protected $_type = Sourcemap_Form_Field::SELECT;

    protected function _makeInput() {
        return Form::select($this->name(), 
            $this->options(), $this->selected(),
            array(
                'class' => $this->css_class()
            )
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
