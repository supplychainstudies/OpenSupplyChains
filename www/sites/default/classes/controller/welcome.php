<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'welcome';

    public function action_index() {
        /*header('Content-Type: text/plain');
        $ip = '18.85.2.115';
        print "$ip\n";
        die(
            print_r(
                Sourcemap_Ip::find_ip($ip),
                true
            )
        );*/
        $this->layout->page_title = 'Welcome to Sourcemap.';
        $this->template->supplychains = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->limit(10)
            ->find_all();
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-working', 'sourcemap-social'
        );
        $this->layout->styles = array(
            'sites/default/assets/styles/reset.css', 
            'sites/default/assets/styles/sourcemap.less?v=2'
        );
    }
}
