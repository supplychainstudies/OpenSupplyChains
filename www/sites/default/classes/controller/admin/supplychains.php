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
	$attributes = array();
	foreach($supplychains as $supplychain) {
	    $supplychains_array[$iterator]['owner'] = $supplychain->owner->username;
	    $attributes[] = $supplychain->attributes->find_all()->as_array(null, array('id', 'supplychain_id', 'key', 'value'));	
	    $iterator++;
	}
	
	$iterator = 0;
	foreach($attributes as $attribute) {
	    if($attribute[0]['supplychain_id'] == $supplychains_array[$iterator]['id']){
		$supplychains_array[$iterator]['key'] = $attribute[0]['key'];
	    }
	    $iterator++;
	}


	$supplychain_count = $supplychain->count_all();

	$this->template->page_links = $pagination->render();
	$this->template->supplychains = $supplychains_array;
        $this->template->offset = $pagination->offset;

	Message::instance()->set('Total users '.$supplychain_count);
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Supply Chains', 'admin/supplychains');

    }


    public function action_details($id) {
	$this->template = View::factory('admin/supplychains/details');
	
	$supplychain = ORM::factory('supplychain', $id);	

	$stop_count = $supplychain->stops->count_all();
	$hop_count = $supplychain->hops->count_all();

	$attribute= $supplychain->attributes->find_all()->as_array(null, 'key');

	$this->template->stop_count = $stop_count;
	$this->template->hop_count = $hop_count;
	$this->template->attribute_key = $attribute[0];
	
	Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Supply Chains', 'admin/supplychains')
            ->add(ucwords($attribute[0]), 'admin/supplychains/'.$id);
	
    }
}
