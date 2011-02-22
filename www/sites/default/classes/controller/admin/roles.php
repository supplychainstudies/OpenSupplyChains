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
	    $pagination = Pagination::factory(array(
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
	    
	    Message::instance()->set('Total roles '.$role_count);
	    Breadcrumbs::instance()->add('Management', 'admin/')
		->add('Roles', 'admin/roles');
	} else {
	    $this->request->redirect('auth/');
	}
    }
    
    public function action_create_role() {

	$post = Validate::factory($_POST);
	$post->rule('role', 'not_empty')
	    ->rule('description', 'not_empty')
	    ->filter(true, 'trim');
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
	    $roles = ORM::factory('role')->find_all()->as_array(null, 'name');
	    
	    if(!in_array($post->role, $roles)) {
		$create_role = ORM::factory('role');
		$create_role->name = $post->role;
		$create_role->description = $post->description;
		$create_role->save();
		
	    } else {
		Message::instance()->set('Role already exists.');
	    }
	}

	$this->request->redirect("admin/roles/");
    }

    public function action_delete_role_entry($id) {
	if($this->current_user && $this->current_user->has('roles', $this->admin)) {  
	    $role = ORM::factory('role')->where('id', '=', $id)->find();
	    $role->delete();
	    
	    $this->request->redirect("admin/roles/");
	} else {
	    $this->request->redirect('auth/');
	}
    }

  }
