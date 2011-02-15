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

	    $role = ORM::factory('role');
	    $page = max($this->request->param('page'), 1);
	    $items = 5;
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

    }

    public function action_create_role() {
	$this->request->redirect("admin/roles/");
    }


  }
