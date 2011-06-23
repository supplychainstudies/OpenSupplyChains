<?php
class Controller_Edit extends Sourcemap_Controller_Map {
    public $template = 'edit';

    public function action_index($supplychain_id=false) {
        if(!$supplychain_id) $this->request->redirect('home');
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                $supplychain = $supplychain->kitchen_sink($supplychain->id);

                // create the form object and add fields
                $form = Sourcemap_Form::factory('update')
                    ->method('post')->action('edit/'.$supplychain_id)
                    ->input('title', 'Title')
                    ->input('teaser', 'Short Description')
                    ->input('tags', 'Tags')
                    ->textarea('description', 'Long Description')
                    ->select('category', 'Category')
                    ->submit('update', 'Update');

                $form->field('category')->option(0, 'None');

                $form->field('title')
                    ->add_class('required');
                if(isset($supplychain->attributes->title))
                    $form->field('title')->value($supplychain->attributes->title);

                $form->field('teaser')
                    ->add_class('required');
                if(isset($supplychain->attributes->teaser))
                    $form->field('teaser')->value($supplychain->attributes->teaser);

                $form->field('tags')
                    ->add_class('tags');
                if(isset($supplychain->attributes->tags))
                    $form->field('tags')->value($supplychain->attributes->tags);

                // fetch the taxonomy tree and use first level
                $taxonomy = Sourcemap_Taxonomy::load_tree();
    
                $cats = $form->field('category')->option(0, 'None');
                $valid_cats = array(0);
                foreach($taxonomy->children as $ti => $t) {
                    $valid_cats[] = $t->data->id;
                    $cats->option($t->data->id, $t->data->name);
                }
                $form->field('category')->value($supplychain->category);

                if(strtolower(Request::$method) === 'post') {
                    $post = Validate::factory($_POST);
                    $post->rule('title', 'not_empty')
                        ->rule('teaser', 'not_empty')
                        ->rule('teaser', 'min_length', array(8))
                        ->rule('teaser', 'max_length', array(140))
                        ->rule('tags', 'regex', array('/^(\s+)?(\w+(\s+)?)*$/'))
                        ->filter('category', 'intval')
                        ->rule('category', 'in_array', array($valid_cats));
                    if($post->check()) {
                        $title = $post['title'];
                        $teaser = $post['teaser'];
                        $tags = Sourcemap_Tags::join(Sourcemap_Tags::parse($post['tags']));
                        $category = $post['category'];
                        if($category) $supplychain->category = $category;
                        else $category = null;
                        $supplychain->attributes->title = $title;
                        $supplychain->attributes->teaser = $teaser;
                        $supplychain->attributes->tags = $tags;
                        try {
                            ORM::factory('supplychain')->save_raw_supplychain($supplychain, $supplychain->id);
                            Message::instance()->set('Map updated.', Message::SUCCESS);
                            return $this->request->redirect('home');
                        } catch(Exception $e) {
                            $this->request->status = 500;
                            Message::instance()->set('Couldn\t update your supplychain. Please contact support.');
                        }
                    } else {
                        Message::instance()->set('Please correct the errors below.');
                        $form->errors($post->errors('forms/create'));
                    }
                }

                $this->template->supplychain = $supplychain;
                $this->template->form = $form;
            } else {
                Message::instance()->set('You\'re not allowed to edit that map.');
                $this->request->redirect('home');
            }
        } else {
            Message::instance()->set('That map does not exist.');
            $this->request->redirect('home');
        }
    }
}
