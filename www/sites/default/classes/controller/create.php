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

        $f = Sourcemap_Form::factory('create')
            ->method('post')
            ->action('create');

        $f->input('title', 'Title')
            ->input('teaser', 'Short Description')
            ->input('tags', 'Tags')
            ->select('category', 'Category')
            ->submit('create', 'Create');

        $f->field('title')
            ->add_class('required');

        $f->field('teaser')
            ->add_class('required');

        $f->field('tags')
            ->add_class('tags');

        $taxonomy = Sourcemap_Taxonomy::load_tree();
    
        $cats = $f->field('category')->option(0, 'None');
        foreach($taxonomy->children as $ti => $t)
            $cats->option($t->data->id, $t->data->name);

        $f->field('category')
            ->selected(0);


        $this->template->create_form = $f;

    }
}
  


