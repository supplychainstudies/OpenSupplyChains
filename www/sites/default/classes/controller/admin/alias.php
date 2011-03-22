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
	    
	   
	    
	    $post = Validate::factory($_POST);
	    $post->rule('site', 'not_empty')
		->rule('alias', 'not_empty')
		->rule('supplychain_id', 'not_empty')
		->filter(true, 'trim');
	    
	    if(strtolower(Request::$method) === 'post' && $post->check()) {
		$check = false;
		$post = (object)$post->as_array();
		
		$site_added = $post->site;
		$alias_added = $post->alias;
		$id = $post->supplychain_id;
				
		// check if the alias already exists, if not add new alias
		
		$supplychain_alias = ORM::factory('supplychain_alias');
		$supplychain_alias->supplychain_id = $id;
		$supplychain_alias->site = $site_added;
		$supplychain_alias->alias = $alias_added;
		try {
		    $supplychain_alias->save();
		} catch(Exception $e) {
		    Message::instance()->set('Could not create alias. Violates the unique (site, alias)');
		}
		$this->request->redirect('admin/alias');
		
	    }

	    
	} 
	
	Message::instance()->set('Total Aliases '.$supplychain_alias_count);
	Breadcrumbs::instance()->add('Management', 'admin/')
	    ->add('Aliases', 'admin/alias');
	
    }


    public function action_delete_supplychain_alias($id) {
	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $supplychain_alias = ORM::factory('supplychain_alias', $id);
	    $supplychain_alias->delete();
	    
	    $this->request->redirect("admin/alias/");
	} 
    }
	

  }
