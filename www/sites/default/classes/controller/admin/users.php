<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */



 class Controller_Admin_Users extends Sourcemap_Controller_Layout {

    public $layout = 'admin';
    public $template = 'users/userslist';

    
      public function action_index() {

	  $user = ORM::factory('user');
	  $test = $user->find_all()->as_array('id', array('email', 'username', 'last_login'));
	  $page = max($this->request->param('page'), 1);
	  $items = 3;
	  $offset = ($items * ($page - 1));
	  $count = $user->count_all();
	  $pagination = Pagination::factory(
					    array('current_page' => array('source' => 'route', 'key' => 'page'),
						  'total_items' => $user->count_all(),
						  'items_per_page' => $items,
						  ));
	
	   
	  $this->template->users = $user->order_by('username', 'ASC')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all()->as_array('id', array('email', 'username'));

	  $this->template->page_links = $pagination->render();
	   
	  


            
        }
      

}
