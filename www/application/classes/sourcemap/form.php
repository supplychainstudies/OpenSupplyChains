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

// TODO: groups -> fieldsets
             ; 
class Sourcemap_Form {
    
    public static $use_templates = true;

    const ENCTYPE_FORM = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULT = 'multipart/form-data';

    protected $_name = 'sourcemap-form';
    protected $_method = 'post';
    protected $_action = '';
    protected $_enctype = null;

    protected $_validate = null;
    protected $_messages_file = null;
    protected $_config_file = null;

    protected $_fields = array();
    protected $_css_class = '';

    public function __construct() {
        $this->enctype(self::ENCTYPE_FORM);
    }

    public function __toString() {
        if(self::$use_templates) {
            try {
                $s = (string)View::factory('form', array('form' => $this));
            } catch(Exception $e) {
                $s = $e->getMessage();
            }
        } else {
            $s = Form::open($this->action(), array(
                'enctype' => $this->enctype(),
                'method' => $this->method(),
                'class' => $this->css_class()
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

    public function messages_file($msgs=null) {
        return $this->_accessor('_messages_file', $msgs);
    }


    public function config_file($p=null) {
        if(!$this->field('_form_id'))
            $this->field('_form_id', Sourcemap_Form_Field::HIDDEN);
        $this->field('_form_id')->value($p);
        return $this->_accessor('_config_file', $p);
    }

    public static function from_array($arr) {
        // TODO: wildcards
        $f = new Sourcemap_Form();
        if(isset($arr['messages_file']) && $arr['messages_file'])
            $f->messages_file($arr['messages_file']);
        if(isset($arr['fields']) && is_array($arr['fields'])) {
            foreach($arr['fields'] as $fnm => $fdef) {
                $new_field = Sourcemap_Form_Field::from_array($fnm, $fdef);
                if(isset($fdef['attributes']) && is_array($fdef['attributes'])) {
                    foreach($fdef['attributes'] as $k => $v) $new_field->html_attr($k, $v);
                }
                call_user_func_array(array($f, 'set_field'), array($fnm, $new_field));
            }
        }
        if(isset($arr['rules']) && is_array($arr['rules'])) {
            foreach($arr['rules'] as $fnm => $rdefs) {
                $field = $f->field($fnm);
                foreach($rdefs as $ri => $rdef) {
                    call_user_func_array(array($field, 'rule'), $rdef);
                }
            }
        }
        return $f;
    }

    public static function load($p) {
        $inclp = array();
        $inclp[] = SOURCEMAP_SITES_PATH.Sourcemap::site().'/forms/';
        $inclp[] = APPPATH.'forms/';
        $ptok = preg_split('/\//', $p, -1, PREG_SPLIT_NO_EMPTY);
        foreach($ptok as $pt) 
            if(!preg_match('/^[A-Za-z0-9]+/', $pt))
                return false;
        $p = join('/', $ptok);
        $arr = false;
        foreach($inclp as $ip) {
            if(file_exists($ip.$p.'.php')) {
                $arr = @include($ip.$p.'.php');
                if(!$arr) 
                    throw new Exception('Could not load form config: '.$ip.$p);
                break;
            }
        }
        if($arr) {
            $form = self::from_array($arr);
            $form->config_file($p);
        } else {
            $form = false;
        }
        return $form;
    }
 
    public static function factory($load_from=null) {
        $instance = new Sourcemap_Form();
        return $instance;
    }

    public function validate($arr=array()) {
        if($arr instanceof Validate) {
            $this->_validate = $arr;
        } else {
            $arr = $arr ? $arr : array();
            $this->_validate = Validate::factory($arr);
            $rules = $this->rules();
            foreach($rules as $fnm => $frules) {
                foreach($frules as $frule) {
                    $frargs = array();
                    if(count($frule) == 2) list($fr, $frargs) = $frule;
                    elseif(count($frule) == 1) $fr = $frule[0];
                    else continue;
                    $this->_validate->rule($fnm, $fr, $frargs);
                }
            }
        }
        $vo = $this->_validate;
        $this->errors(array());
        $this->values($vo->as_array());
        if($vo->check(true)) {
            return true;
        } else {
            $this->errors($vo->errors($this->messages_file()));
            return false;
        }
    }

    public function rules($rules=null) {
        if($rules === null) {
            $rules = array();
            foreach($this->_fields as $nm => $f) {
                $rules[$nm] = $f->rules();
            }
            return $rules;
        } else {
            foreach($this->_fields as $nm => $f) {
                if(isset($rules[$nm])) {
                    $f->rules($rules[$nm]);
                } else {
                    $f->rules(array());
                }
            }
            return $this;
        }
    }

    public function values($vs=null) {
        $exclude = array('_form_id');
        if($vs === null) {
            $vs = array();
            foreach($this->_fields as $nm => $f) {
                if(in_array($nm, $exclude)) continue;
                $vs[$nm] = $f->value();
            }
            return $vs;
        } else {
            foreach($this->_fields as $nm => $f) {
                if(in_array($nm, $exclude)) continue;
                if(isset($vs[$nm])) {
                    $f->value($vs[$nm]);
                } else {
                    $f->value(null, true);
                }
            }
            return $this;
        }
    }

    public function errors($es=null) {
        if($es === null) {
            $es = array();
            foreach($this->_fields as $k => $f) {
                $e = $f->errors();
                if($e) {
                    $es[$k] = array();
                    $e = is_array($e) ? $e : array($e);
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
        $fo->form($this);
        $this->_fields[$f] = $fo;
        return $this;
    }

    public function get_fields() {
        return $this->_fields;
    }

    public function field($f, $t=null, $l=null, $wt=null) { // name, type, default value, weight
        if($t || $l || $wt) {
            $nm = $f;
            $f = Sourcemap_Form_Field::factory($t);
            $f->form($this);
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

    public function has_class($cls) {
        return is_array($this->_css_class) && in_array($cls, $this->_css_class);
    }

    public function add_class($cls) {
        if(!is_array($this->_css_class))
            $this->_css_class = array();
        if(!$this->has_class($cls))
            $this->_css_class[] = $cls;
        return $this;
    }

    public function css_class() {
        if(!is_array($this->_css_class))
            return null;
        return join(' ', $this->_css_class);
    }

    public function remove_class($cls) {
        if($this->has_class($cls)) {
            $k = array_search($cls, $this->_css_class, true);
            if($k !== false) {
                array_splice($this->_css_class, $k, 1); 
            }
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

    public function textarea($f, $v=null, $wt=null) {
       return $this->field($f, Sourcemap_Form_Field::TEXTAREA, $v, $wt);
    }

    public function checkbox($f, $v=null, $wt=null) {
       return $this->field($f, Sourcemap_Form_Field::CHECKBOX, $v, $wt);
    } 
    
    public function recaptcha($f, $v=null, $wt=null) {  
       return $this->field($f, Sourcemap_Form_Field::RECAPTCHA, $v, $wt);
    }
}
