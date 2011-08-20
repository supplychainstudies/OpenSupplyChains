<?php
class Controller_Info extends Sourcemap_Controller_Layout {
    public $layout = 'base';
    public $template = 'info/info';
    
    public function action_index() {}    
    public function action_api() { $this->template = View::factory('info/api'); }
    public function action_terms() { $this->template = View::factory('info/terms'); }
    public function action_dmca() { $this->template = View::factory('info/terms'); }
    public function action_privacy() { $this->template = View::factory('info/privacy'); }
    
}

