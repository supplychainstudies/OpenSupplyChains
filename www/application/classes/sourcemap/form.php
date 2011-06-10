<?php
// todo: groups -> fieldsets

class Sourcemap_Form {
    
    public static $use_templates = false;

    const ENCTYPE_FORM = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULT = 'multipart/form-data';

    protected $_name = 'sourcemap-form';
    protected $_method = 'post';
    protected $_action = '';
    protected $_enctype = null;

    protected $_fields = array();

    public function __construct() {
        $this->enctype(self::ENCTYPE_FORM);
    }

    public function __toString() {
        if(self::$use_templates) {
            throw new Exception('Template-based forms not implemented.');
        } else {
            $s = Form::open($this->action(), array(
                'enctype' => $this->enctype(),
                'method' => $this->method()
            ));
            $s .= "\n";
            foreach($this->_fields as $nm => $f) {
                $s .= (string)$f;
            }
            $s .= Form::close();
        }
        return $s;
    }

    protected function _accessor($p, $v=null) {
        if($v !== null) $this->$p = $v;
        else return $this->$p;
        return $this;
    }

    public function name($nm=null) {
        return $this->_accessor('_name', $nm);
    }

    public function method($m=null) {
        return $this->_accessor('_method', $m);
    }

    public function action($a=null) {
        return $this->_accessor('_action', $a);
    }

    public function enctype($ct=null) {
        return $this->_accessor('_enctype', $ct);
    }
 
    public static function factory($init_from=null) {
        $instance = new Sourcemap_Form();
        return $instance;
    }

    public function validate($vo) {
        $this->errors(array());
        if($vo->check()) {
            return true;
        } else {
            $this->errors($vo->errors());
            return false;
        }
    }

    public function values($vs) {
        foreach($this->_fields as $nm => $f) {
            if(isset($vs[$nm])) {
                $f->value($vs[$nm]);
            } else {
                $f->value(null, true);
            }
        }
        return $this;
    }

    public function errors($es=null) {
        if($es === null) {
            $es = array();
            foreach($this->_fields as $k => $f) {
                $e = $f->errors();
                if($e) {
                    $es[$k] = array();
                    foreach($e as $ei => $ee) $es[$k][] = $ee;
                }
            }
            return $es;
        }
        foreach($this->_fields as $k => $f) {
            if(isset($es[$k])) {
                $this->_fields[$k]->errors($es[$k]);
            } else {
                $this->_fields[$k]->errors(array());
            }
        }
        return $this;
    }

    public function get_field($f) {
        return isset($this->_fields[$f]) ? $this->_fields[$f] : null;
    }

    public function set_field($f, $fo) {
        $this->_fields[$f] = $fo;
        return $this;
    }

    public function field($f, $t=null, $l=null, $wt=null) { // name, type, default value, weight
        if($t || $l || $wt) {
            $nm = $f;
            $f = Sourcemap_Form_Field::factory($t);
            $this->_fields[$nm] = $f;
            $f->name($nm);
            $f->label($l);
            if($wt !== null) $f->weight($wt);
        } else {
            $f = $this->get_field($f);
            return $f;
        }
        return $this;
    }

    public function input($f, $l=null, $wt=null) {
        return $this->field($f, Sourcemap_Form_Field::INPUT, $l, $wt);
    }
    
    public function password($f, $l=null, $wt=null) {
       return $this->field($f, Sourcemap_Form_Field::PASSWORD, $l, $wt);
    }

    public function select($f, $l=null, $wt=null) {
        return $this->field($f, Sourcemap_Form_Field::SELECT, $l, $wt);
    }

    public function submit($f, $v=null, $wt=null) {
       return $this->field($f, Sourcemap_Form_Field::SUBMIT, $v, $wt);
    }
}
