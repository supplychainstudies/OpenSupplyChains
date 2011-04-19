<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Admin_Roles extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/roles/list';


    public function action_index() {

	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $role = ORM::factory('role');
	    $page = max($this->request->param('page'), 1);
	    $items = 20;
	    $offset = ($items * ($page - 1));
	    $count = $role->count_all();
	    $pagination = Pagination::factory(
		array(
		    'current_page' => array(
			'source' => 'query_string', 
			'key' => 'page'
			),
		    'total_items' => $role->count_all(),
		    'items_per_page' => $items,
		    ));
	    $this->template->roles = $role->order_by('name', 'ASC')
		->limit($pagination->items_per_page)
		->offset($pagination->offset)
		->find_all()->as_array(null, array('id', 'name', 'description'));
	    $this->template->page_links = $pagination->render();
	    $this->template->offset = $pagination->offset;
	    
	    $role_count = $role->count_all();
	    
	    Breadcrumbs::instance()->add('Management', 'admin/')
		->add('Roles', 'admin/roles');
	}
    }
    
    public function action_create_role() {

	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $post = Validate::factory($_POST);
	    $post->rule('role', 'not_empty')
		->rule('description', 'not_empty')
		->filter('role', 'strip_tags')
		->filter('description', 'strip_tags')
		->filter(true, 'trim');
	    if(strtolower(Request::$method) === 'post' && $post->check()) {
		$post = (object)$post->as_array();
		$create_role = ORM::factory('role');
		$create_role->name = $post->role;
		$create_role->description = $post->description;
		try {
		    $create_role->save();
		    Message::instance()->set('Role created successfully!');
		} catch(Exception $e) {
		    Message::instance()->set('Role already exists.');
		}
	    }
	    
	    $this->request->redirect("admin/roles/");
	}
    }

    public function action_delete_role_entry($id) {
	
	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $role = ORM::factory('role', $id);
	    try {
		$role->delete();
		Message::instance()->set('Role deleted.');
	    } catch(Exception $e) {
		Message::instance()->set('Role could not be deleted.');
	    }
	    
	    $this->request->redirect("admin/roles/");
	} 
    }

  }
