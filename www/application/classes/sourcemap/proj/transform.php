<?php
class Sourcemap_Proj_Transform {
    
    protected $_proj = null;

    public function __construct(Sourcemap_Proj_Projection $proj) {
        $this->_proj = $proj;
        $this->init();
    }

    public function init() {}
    public function forward($pt) { return $pt; }
    public function inverse($pt) { return $pt; }
}
