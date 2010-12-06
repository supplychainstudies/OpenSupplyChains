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
  public $template = 'users/master';

  public function action_index() {
    $user = ORM::factory('user');
    $page = max($this->request->param('page'), 1);
    $items = 3;
    $offset = ($items * ($page - 1));
    $count = $user->count_all();
    $pagination = Pagination::factory(
				      array('current_page' => array('source' => 'route', 'key' => 'page'),
					    'total_items' => $user->count_all(),
					    'items_per_page' => $items,
					    ));

    $this->template->main_content = new View('users/userslist');
    $this->template->main_content->users = $user->order_by('username', 'ASC')
      ->limit($pagination->items_per_page)->offset($pagination->offset)
      ->find_all()->as_array('id', array('id', 'email', 'username'));
    $this->template->main_content->page_links = $pagination->render();
    $this->template->main_content->offset = $pagination->offset;
    
  }
      public function action_single($id) {
	
	$this->template->main_content = new View('users/usersingle');
	$user = ORM::factory('user', $id);
	$roles = array();
	foreach($user->roles->find_all()->as_array() as $i => $role) {
	  $roles[] = $role->as_array();
	}
	$query = "SELECT  * FROM role ";

	$all_roles = Db::query(Database::SELECT, $query)
          ->execute()
          ->as_array();
	
	$this->template->main_content->user = $user;
	$this->template->main_content->roles = $roles;
	$this->template->main_content->all_roles = $all_roles;
	
	
      }
      

}
