<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Kilroy
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Admin_Featured extends Controller_Admin { 
 

    public $layout = 'admin';
    public $template = 'admin/featured/list';
  

    public function action_index() {    
    
        $supplychain = ORM::factory('supplychain');
        $page = max($this->request->param('page'), 1);
        $items = 20;
        $offset = ($items * ($page - 1));
        $supplychain = $supplychain->where(DB::expr('flags & '.Sourcemap::FEATURED), '>', 0);
        $supplychain = $supplychain->and_where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0);
        $pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $supplychain->reset(false)->count_all(),
            'items_per_page' => $items
        ));
        $supplychains = $supplychain->order_by('modified', 'ASC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all();        
        $supplychains_array = $supplychains->as_array('id', array('id', 'created')); 
        
        $attributes = array();
        foreach($supplychains as $supplychain) {
            $scid = $supplychain->id;
            $supplychains_array[$scid] = (array)$supplychains_array[$scid];
            $supplychains_array[$scid]['owner'] = $supplychain->owner->username;
            $supplychains_array[$scid]['created'] = date("F j, Y, g:i a", $supplychains_array[$scid]['created']);
            $supplychains_array[$scid]['attributes'] = $supplychain->attributes->find_all()->as_array('key', 'value');
        }

        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        $this->template->list = $supplychains_array;

        Breadcrumbs::instance()->add('Management', 'admin/')
        ->add('Featured Supply Chains', 'admin/featured');
    }

    public function action_add() {
        $post = Validate::factory($_POST);
        $post->rule('supplychain_id', 'is_numeric');
        $sc = ORM::factory('supplychain', $post['supplychain_id']);
        if($sc->loaded()) {
            if(!($sc->other_perms & Sourcemap::READ)) {
                Message::instance()->set('That supplychain isn\'t public.');
                $this->request->redirect('admin/featured');
            }
            $sc->flags = $sc->flags | Sourcemap::FEATURED;
            $sc->save();
            if(Sourcemap_Search_Index::should_index($sc->id))
                Sourcemap_Search_Index::update($sc->id);
            Message::instance()->set('Added featured map.', Message::SUCCESS);
            $this->request->redirect('admin/featured');
        } else {
            Message::instance()->set('That supplychain does not exist.');
            $this->request->redirect('admin/featured');
        }
    }

    public function action_remove($id) {
        $sc = ORM::factory('supplychain', $id);
        if($sc->loaded()) {
            $sc->flags = $sc->flags & ~Sourcemap::FEATURED;
            $sc->save();
            if(Sourcemap_Search_Index::should_index($sc->id))
                Sourcemap_Search_Index::update($sc->id);
            Message::instance()->set('Unfeatured map.', Message::SUCCESS);
            $this->request->redirect('admin/featured');
        } else {
            Message::instance()->set('That supplychain does not exist.');
            $this->request->redirect('admin/featured');
        }
    }

}


