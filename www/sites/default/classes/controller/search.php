<?php
class Controller_Search extends Sourcemap_Controller_Layout {
    public $template = 'search';

    public function action_index() {
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $this->template->search_result = Sourcemap_Search::find($_GET, 'simple');
    }
}
