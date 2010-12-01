<?php
class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'welcome';

    public function action_index() {
        $this->template->message = 'Hi.';
    }
}
