<?php
class Controller_Tools_Import extends Sourcemap_Controller_Layout {
    
    public $layout = 'layout';
    public $template = 'tools/import/csv';

    public function action_index() {
        die('no!');
    }

    public function action_csv() {
        $this->layout->scripts = array(
            'sourcemap-core'
        );
        $this->layout->styles = array(
            'assets/styles/style.css',
            'assets/styles/sourcemap.less?v=2'
        );
    }
}
