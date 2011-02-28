<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Admin_Alias extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/alias/list';


    public function action_index() {

	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $supplychain_alias = ORM::factory('supplychain_alias');
	    $page = max($this->request->param('page'), 1);
	    $items = 20;
	    $offset = ($items * ($page - 1));
	    $count = $supplychain_alias->count_all();
	    $pagination = Pagination::factory(array(
						  'current_page' => array(
						      'source' => 'query_string', 
						      'key' => 'page'
						      ),
						  'total_items' => $supplychain_alias->count_all(),
						  'items_per_page' => $items,
						  ));
	    $this->template->supplychain_alias = $supplychain_alias->limit($pagination->items_per_page)
		->offset($pagination->offset)
		->find_all()->as_array(null, array('id', 'site', 'alias', 'supplychain_id'));
	    $this->template->page_links = $pagination->render();
	    $this->template->offset = $pagination->offset;
	    
	    $supplychain_alias_count = $supplychain_alias->count_all();
	    
	    Message::instance()->set('Total Aliases '.$supplychain_alias_count);
	    Breadcrumbs::instance()->add('Management', 'admin/')
		->add('Aliases', 'admin/alias');
	} else {
	    $this->request->redirect('auth/');
	}
    	
    }



    public function action_delete_supplychain_alias($id) {
	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $supplychain_alias = ORM::factory('supplychain_alias', $id);
	    $supplychain_alias->delete();
	    
	    $this->request->redirect("admin/alias/");
	} else {
	    $this->request->redirect('auth/');
	}	
    }
	

  }
