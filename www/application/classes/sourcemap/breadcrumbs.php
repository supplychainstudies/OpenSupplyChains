<?php
class Sourcemap_Breadcrumbs {

    public $_crumbs = array();
    public $_default_view = 'breadcrumbs';

    public function add($label, $uri=null, $class=null) {
        $this->_crumbs[] = (object)array(
            'label' => $label, 'uri' => $uri, 'class' => $class=null
        );
        return $this;
    }

    public function set($crumbs) {
        $this->_crumbs = array();
        foreach($crumbs as $i => $crumb) {
            call_user_func_array(array($this, 'add'), $crumb);
        }
        return $this;
    }

    public function get() {
        return $this->_crumbs;
    }

    public function render($view=null) {
        $view = $view ? $view : $this->_default_view;
        $view = View::factory($view);
        $view->set('breadcrumbs', $this->_crumbs);
        return $view->render();
    }
}
