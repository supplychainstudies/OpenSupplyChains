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
            $this->request->redirect('auth');
        }
        
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template'
        );

        $f = Sourcemap_Form::factory('create')
            ->method('post')
            ->action('create');

        $f->input('title', 'Title')
            ->input('teaser', 'Short Description')
            ->input('tags', 'Tags')
            ->select('category', 'Category')
            ->checkbox('public', 'Public')
            ->submit('create', 'Create');

        $f->field('title')
            ->add_class('required');

        $f->field('teaser')
            ->add_class('required');

        $f->field('tags')
            ->add_class('tags');

        $taxonomy = Sourcemap_Taxonomy::load_tree();
    
        $cats = $f->field('category')->option(0, 'None');
        $valid_cats = array(0);
        foreach($taxonomy->children as $ti => $t) {
            $valid_cats[] = $t->data->id;
            $cats->option($t->data->id, $t->data->name);
        }

        $f->field('category')
            ->selected(0);


        $this->template->create_form = $f;

        if(strtolower(Request::$method) === 'post') {
            $p = Validate::factory($_POST);
            $f->values($_POST);
            $p->rule('title', 'not_empty')
                ->rule('teaser', 'not_empty')
                ->rule('teaser', 'min_length', array(8))
                ->rule('teaser', 'max_length', array(140))
                ->rule('tags', 'regex', array('/^(\s+)?(\w+(\s+)?)*$/'))
                ->filter('category', 'intval')
                ->rule('category', 'in_array', array($valid_cats));
            if($p->check()) {
                // create!
                $title = $p['title'];
                $teaser = $p['teaser'];
                $tags = Sourcemap_Tags::join(Sourcemap_Tags::parse($p['tags']));
                $category = $p['category'];
                $public = isset($_POST['public']) ? Sourcemap::READ : 0;
                $raw_sc = new stdClass();
                if($category) $raw_sc->category = $category;
                $raw_sc->attributes = new stdClass();
                $raw_sc->attributes->title = $title;
                $raw_sc->attributes->teaser = $teaser;
                $raw_sc->attributes->tags = $tags;
                $raw_sc->stops = array();
                $raw_sc->hops = array();
                $raw_sc->user_id = Auth::instance()->get_user()->id;
                $raw_sc->other_perms = 0;
                if($public)
                    $raw_sc->other_perms |= $public;
                else
                    $raw_sc->other_perms &= ~Sourcemap::READ;
                try {
                    $new_scid = ORM::factory('supplychain')->save_raw_supplychain($raw_sc);
                    return $this->request->redirect('map/view/'.$new_scid);
                } catch(Exception $e) {
                    $this->request->status = 500;
                    Message::instance()->set('Couldn\t create your supplychain. Please contact support.');
                }
            } else {
                Message::instance()->set('Correct the errors below.');
                $f->errors($p->errors('forms/create'));
            }
        }

    }
}
