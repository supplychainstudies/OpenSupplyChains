<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'welcome';

    public function action_index() {
        $this->template->supplychains = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->find_all();
    }
}
