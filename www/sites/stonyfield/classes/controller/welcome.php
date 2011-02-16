<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {
    public $layout = 'stonyfield-demo';
    public $template = 'welcome';

    public function action_index() {
        $this->layout->styles = array(
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css'
        );
        $this->layout->scripts = array(
            'sourcemap-jquery', 
            'stonyfield-embed', 
            'sourcemap-map'
        );
        $alias = ORM::factory('supplychain_alias')
            ->where('site', '=', 'stonyfield')
            ->where('alias', '=', 'yogurt')
            ->find_all()
            ->as_array('alias', 'supplychain_id');
        $scids = array();
        if($alias) $scids[] = $alias['yogurt'];
        $this->layout->supplychain_ids = $scids;
    }
}
