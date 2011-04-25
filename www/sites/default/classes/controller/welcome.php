<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'welcome';

    public function action_index() {
        $this->layout->page_title = 'Welcome to Sourcemap.';
        $this->template->supplychains = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->limit(10)->order_by('created', 'desc')
            ->find_all();
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-working', 'sourcemap-social'
        );
        $this->layout->styles = array(
            'sites/default/assets/styles/reset.css', 
            'assets/styles/general.less'
        );
    }
}
