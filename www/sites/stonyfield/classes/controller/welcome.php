<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {
    public $layout = 'stonyfield-demo';
    public $template = 'welcome';

    public function action_index($scalias=null) {
        if(is_null($scalias) || empty($scalias)) $scalias = 'yogurt';
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
            ->where('alias', '=', $scalias)
            ->find_all()
            ->as_array('alias', 'supplychain_id');
        $alias_mapping = array(
            'yogurt' => 'sf-yogurt-all',
            'dairy' => 'sf-yogurt-d',
            'sweeteners' => 'sf-yogurt-fs',
            'other' => 'sf-yogurt-o'
        );
        if(!isset($alias[$scalias])) die('That map does not exist.');
        $scid = $alias[$scalias];
        $this->layout->supplychain_id = $scid;
        $this->template->scalias = $scalias;
    }
}
