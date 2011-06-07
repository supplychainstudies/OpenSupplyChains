<?php
/**
 * Description Create a Map page
 * @package    Sourcemap
 * @author     Alex Ose 
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */

class Controller_Create extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'create';
    
    public function action_index() {
        if(!Auth::instance()->get_user())
            return $this->_forbidden('You must be logged in to create a map.');

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-create' 
        );
        $this->layout->styles = array(
            'assets/styles/style.css', 
            'assets/styles/sourcemap.less?v=2'
        );
    }
}
  


