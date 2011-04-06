<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */



class Controller_Upload extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'upload';

    public function action_index() {
	$this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-working', 'sourcemap-upload'
        );
        $this->layout->styles = array(
            'assets/styles/style.css', 
            'assets/styles/sourcemap.less?v=2'
        );


    }
}
