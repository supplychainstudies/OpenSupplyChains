<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'welcome';

    public function action_index() {
        $this->layout->page_title = 'Welcome to Sourcemap.';
        $supplychain_rows = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->limit(12)->order_by('created', 'desc')
            ->find_all()->as_array('id', true);
        foreach($supplychain_rows as $i => $sc) {
            $ks =  ORM::factory('supplychain')->kitchen_sink($sc->id);
            $ks->owner = ORM::factory('user', $sc->user_id)->find();
            $supplychains[] = $ks;
        }
        $this->template->supplychains = $supplychains;
        $this->layout->scripts = array(
        );
        $this->layout->styles = array(
        );
    }
}
