<?php
class Controller_Admin_Taxonomy extends Controller_Admin {
    
    public $template = 'admin/taxonomy';

    public function action_index() {
        $this->template->tree = Sourcemap_Taxonomy::load_tree(null);
    }

    public function action_add() {
        if(!(strtolower(Request::$method) == 'post')) {
            Message::instance()
                ->set('I\'m not sure what you\'re trying to do, but stop it.');
            $this->request->redirect('admin/taxonomy');
        }
        $post = Validate::factory($_POST);
        $post->rule('parent', 'not_empty')
            ->rule('parent', 'is_numeric')
            ->rule('title', 'not_empty')
            ->rule('title', 'min_length', array(4))
            ->rule('title', 'max_length', array(32))
            ->rule('name', 'not_empty')
            ->filter('name', 'strtolower')
            ->rule('name', 'min_length', array(4))
            ->rule('name', 'max_length', array(16))
            ->rule('description', 'not_empty');
        if($post->check()) {
            if($post['parent'] > 0) {
                $p = ORM::factory('category', $post['parent']);
                if($p->loaded()) {
                    $p->add_child((object)$post->as_array());
                    Message::instance()->set('Added.', Message::SUCCESS);
                    $this->request->redirect('admin/taxonomy');
                } else {
                    Message::instance()->set('That category does not exist.');
                    $this->request->redirect('admin/taxonomy');
                }
            } else {
                ORM::factory('category')->add_child((object)$post->as_array(), true);
                Message::instance()->set('Added.', Message::SUCCESS);
                $this->request->redirect('admin/taxonomy');
            }
        } else {
            Message::instance()->set('Bad request: '.print_r($post->errors(), true));
            $this->request->redirect('admin/taxonomy');
        }
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Categories', 'admin/taxonomy');

    }

    public function action_edit($id) {

        $term = ORM::factory('category', $id);

        if(!$term->loaded()) {
            Message::instance()->set('Invalid category.');
            $this->request->redirect('admin/taxonomy');
        }

        if(strtolower(Request::$method) == 'post') {
            $post = Validate::factory($_POST);
            $post->rule('title', 'not_empty')
                ->rule('name', 'not_empty');
            if($post->check()) {
                $term->title = $post["title"];         
                $term->name = $post["name"];
                $term->save();
                Message::instance()->set('Category updated.', Message::INFO);
                $this->request->redirect('admin/taxonomy');
            } else {
                Message::instance()->set('Try again.');
                $this->request->redirect('admin/taxonomy/'.$id.'/edit');
            }
        } else {
            $this->template = View::factory('admin/taxonomy/edit');
            $term = ORM::factory('category', (int)$id);
            $this->template->term = $term;
        }
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Categories', 'admin/taxonomy')
            ->add($term->title, 'admin/taxonomy/'.$term->id.'/edit');
    }

    public function action_rm() {
        if(!(strtolower(Request::$method) == 'post')) {
            Message::instance()
                ->set('I\'m not sure what you\'re trying to do, but stop it.');
            $this->request->redirect('admin/taxonomy');
        }
        $post = Validate::factory($_POST);
        $post->rule('taxonomy_id', 'not_empty')
            ->rule('taxonomy_id', 'is_numeric');
        if($post->check()) {
            $t = ORM::factory('category', $post['taxonomy_id']);
            if($t->loaded()) {
                try {
                    $t->drop_subtree();
                    $this->request->redirect('admin/taxonomy');
                } catch(Exception $e) {
                    Message::instance()->set('Could not drop subtree: '.$e->getMessage());
                    $this->request->redirect('admin/taxonomy');
                }
            } else {
                Message::instance()->set('That category does not exist.');
                $this->request->redirect('admin/taxonomy');
            }
        } else {
            Message::instance()
                ->set('Bad request.');
            $this->request->redirect('admin/taxonomy');
        }
    }
}
