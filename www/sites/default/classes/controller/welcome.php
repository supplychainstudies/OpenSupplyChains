<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'welcome';

    public function action_index() {
        /*header('Content-Type: text/plain');
        die(
            print_r(
                Model::factory('iploc')->find('74.125.226.146'),
                true
            )
        );*/
        $this->layout->page_title = 'Welcome to Sourcemap.';
        $this->template->supplychains = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->limit(10)
            ->find_all();
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-working'
        );
        $this->layout->styles = array(
            'sites/default/assets/styles/style.css', 
            'sites/default/assets/styles/sourcemap.less?v=2'
        );
    }
}
