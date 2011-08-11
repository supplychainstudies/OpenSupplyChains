<?php
/**
 * Description Edit map properties page
 * @package    Sourcemap
 * @author     Alex Ose 
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


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

                // Load form template
                $form = Sourcemap_Form::load('/edit');
                $form->action('edit/'.$supplychain->id)->method('post');

                // Populate fields
                $form->field('title')
                    ->add_class('required');
                if(isset($supplychain->attributes->title))
                    $form->field('title')->value($supplychain->attributes->title);

                if(isset($supplychain->attributes->description))
                    $form->field('description')->value($supplychain->attributes->description);

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

                $form->field('publish')->value($supplychain->other_perms & Sourcemap::READ);

                if(strtolower(Request::$method) === 'post') {
                    if($form->validate($_POST)) {
                        $title = $form->get_field('title')->value();
                        $description = $form->get_field('description')->value();
                        $tags = Sourcemap_Tags::join(Sourcemap_Tags::parse($form->get_field('tags')->value()));
                        $category = $form->get_field('category')->value();
                        if($category) $supplychain->category = $category;
                        else $category = null;
                        $public = isset($_POST['publish']) ? Sourcemap::READ : 0;
                        $supplychain->attributes->title = $title;
                        $supplychain->attributes->description = $description;
                        $supplychain->attributes->tags = $tags;
                        if($public)
                            $supplychain->other_perms |= $public;
                        else
                            $supplychain->other_perms &= ~Sourcemap::READ;
                        try {
                            ORM::factory('supplychain')->save_raw_supplychain($supplychain, $supplychain->id);
                            Message::instance()->set('Map updated.', Message::SUCCESS);
                            return $this->request->redirect('view/'.$supplychain->id);
                        } catch(Exception $e) {
                            $this->request->status = 500;
                            Message::instance()->set('Couldn\t update your supplychain. Please contact support.');
                        }
                    } else {
                        Message::instance()->set('Please correct the errors below.');
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

    public function action_visibility($supplychain_id=false) {
        $set_to = null;
        if($supplychain_id && (Request::$method === 'POST')) {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$supplychain->user_id;
                if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                    $p = Validate::factory($_POST);
                    $p->rule('publish', 'regex', array('/(yes|no)/i'))
                        ->rule('publish', 'not_empty');
                    if($p->check()) {
                        $set_to = strtolower($p['publish']) == 'yes';
                    } else {
                        Message::instance()->set('Missing required "publish" parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map doesn\'t exist.');
                $this->request->redirect('/home');
            }
        } elseif(Request::$method === 'GET') {
            $sc = ORM::factory('supplychain', $supplychain_id);
            if($sc->loaded()) {
                $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
                $owner_id = (int)$sc->user_id;
                if($current_user_id && $sc->user_can($current_user_id, Sourcemap::WRITE)) {
                    $g = Validate::factory($_GET);
                    $g->rule('publish', 'regex', array('/(yes|no)/i'))
                        ->rule('publish', 'not_empty');
                    if($g->check()) {
                        $set_to = strtolower($g['publish']) == 'yes';
                    } else {
                        Message::instance()->set('Missing required "publish" parameter.');
                        $this->request->redirect('/home');
                    }
                } else {
                    Message::instance()->set('You don\'t have permission to do that.');
                    $this->request->redirect('/home');
                }
            } else {
                Message::instance()->set('That map does not exist.');
                $this->request->redirect('/home');
            }
        } else {
            Message::instance()->set('Bad request.');
            $this->request->redirect('/home');
        }
        if($set_to !== null) {
            if($set_to === true)
                $sc->other_perms |= $set_to;
            else
                $sc->other_perms &= ~Sourcemap::READ;
            try {
                $sc->save();
                Message::instance()->set('Map updated.', Message::SUCCESS);
                return $this->request->redirect('/home');
            } catch(Exception $e) {
                $this->request->status = 500;
                Message::instance()->set('Couldn\t update your supplychain. Please contact support.');
            }
        }
    }
}
