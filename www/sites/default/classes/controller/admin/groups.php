<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */



class Controller_Admin_Groups extends Controller_Admin {
    
    public $layout = 'admin';
    public $template = 'admin/groups/list';
    
    public function action_index() {
        $usergroup = ORM::factory('usergroup');
        $page = max($this->request->param('page'), 1);
        $items = 5;
        $offset = ($items * ($page - 1));
        $count = $usergroup->count_all();
        $pagination = Pagination::factory(
            array('current_page' => array('source' => 'query_string', 'key' => 'page'),
          'total_items' => $usergroup->count_all(),
          'items_per_page' => $items,
                ));
        $this->template->groups = $usergroup->order_by('name', 'ASC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()->as_array(null, array('id', 'owner_id', 'name'));
        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Groups', 'admin/groups');
    }
    
}
