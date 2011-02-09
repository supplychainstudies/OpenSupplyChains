<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Admin_Supplychains extends Controller_Admin { 
 

    public $layout = 'admin';
    public $template = 'admin/supplychains/list';

    public function action_index() {

	$supplychain = ORM::factory('supplychain');
	$page = max($this->request->param('page'), 1);
	$items = 5;
        $offset = ($items * ($page - 1));
        $count = $supplychain->count_all();
        $pagination = Pagination::factory(
            array('current_page' => array('source' => 'query_string', 'key' => 'page'),
          'total_items' => $supplychain->count_all(),
          'items_per_page' => $items,
                ));
        $supplychains = $supplychain->order_by('id', 'ASC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all();

	$supplychains_array = $supplychains->as_array(null, array('id', 'created'));
	
	$iterator = 0;
	foreach($supplychains as $supplychain) {
	    $supplychains_array[$iterator]['owner'] = $supplychain->owner->username;
	    $iterator++;
	}

	$this->template->page_links = $pagination->render();
	$this->template->supplychains = $supplychains_array;
        $this->template->offset = $pagination->offset;
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Supply Chains', 'admin/supplychains');

    }
}
