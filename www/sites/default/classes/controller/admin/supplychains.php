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
    	const PAGESZ_MIN = 1;
	const PAGESZ_MAX = 25;


    public function action_index() {

	$supplychain = ORM::factory('supplychain');
	
	$limit = isset($_GET['l']) ?
            max(self::PAGESZ_MIN, min(self::PAGESZ_MAX, (int)$_GET['l'])) :
            self::PAGESZ_MAX;
        $offset = isset($_GET['o']) ? (int)$_GET['o'] : 0;

	$supplychains = $supplychain->order_by('id', 'ASC')
		->offset($offset)->limit($limit)
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

	$this->template->limit = $limit;
	$this->template->total = $supplychain_count;
	$this->template->list = $supplychains_array;

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
