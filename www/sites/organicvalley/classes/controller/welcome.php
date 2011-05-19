<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {
    public $layout = 'organicvalley';
    public $template = 'welcome';

    public function action_index($scalias=null) {
        if(is_null($scalias) || empty($scalias)) $scalias = 'yogurt';
        $this->layout->styles = array(
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css'
        );
        $alias = ORM::factory('supplychain_alias')
            ->where('site', '=', 'organicvalley')
            ->where('alias', '=', $scalias)
            ->find_all()
            ->as_array('alias', 'supplychain_id');
        if(!isset($alias[$scalias])) die('That map does not exist.');
        $scid = $alias[$scalias];
        $this->layout->supplychain_id = $scid;
        $this->template->scalias = $scalias;
        $this->template->scid = $scid;
    }

    public function action_milk() {
        $this->template = View::factory('milk');
        $this->layout->scripts = 'organicvalley-imap';
    }
}
