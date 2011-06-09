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

        if(!Auth::instance()->get_user()) {
            Message::instance()->set('You must be logged in to create maps.');
            $this->request->redirect('auth');
        }
        
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 'sourcemap-create' 
        );

        $this->template->taxonomy = Sourcemap_Taxonomy::load_tree();
    }
}
  


