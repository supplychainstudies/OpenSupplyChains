<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Admin_Groups extends Controller_Admin {
    
    public $layout = 'admin';
    public $template = 'admin/groups/list';
    
    public function action_index() {
	
        $usergroup = ORM::factory('usergroup');
        $page = max($this->request->param('page'), 1);
        $items = 5;
        $offset = ($items * ($page - 1));
        $count = $usergroup->count_all();
        $pagination = Pagination::factory(
            array('current_page' => array('source' => 'query_string', 'key' => 'page'),
          'total_items' => $usergroup->count_all(),
          'items_per_page' => $items,
                ));
        $groups = $usergroup->order_by('name', 'ASC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all();
	
	$groups_array = $groups->as_array(null, array('id', 'name'));

	
	$iterator = 0;
	foreach($groups as $group) {
	    $groups_array[$iterator]['owner'] = $group->owner->username;
	    $iterator++;
	}
	

        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
	$this->template->groups = $groups_array;
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Groups', 'admin/groups');
    }
 
    public function action_details($id) {

	$this->template = View::factory('admin/groups/details');
	
	$group = ORM::factory('usergroup', $id);
	
	$group_members = array();
	foreach($group->members->find_all()->as_array() as $i => $user) {
	    $group_members[] = $user->as_array();
	}
	
	$owner = $group->owner->find()->as_array(null, 'username');
	$owner_name =$owner['username'];

	$group_name = $group->find()->as_array(null, 'name');
	$group_name = $group_name['name'];

	$this->template->owner = $owner_name;
	$this->template->group_name = $group_name;
	$this->template->members = $group_members;
	
	Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Groups', 'admin/groups')
            ->add(ucwords($group_name), 'admin/groups/'.$id);
	
    }


    public function action_create_group() {

	$post = Validate::factory($_POST);
	$post->rule('username', 'not_empty')
            ->rule('groupname', 'not_empty')
            ->filter(true, 'trim');
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
	    $create = ORM::factory('usergroup');
	    $name = $post->username;
	    $userid = ORM::factory('user')->where('username', '=', $name)->find_all()->as_array(null, 'id');
	    $create->owner_id = $userid[0];
            $create->name= $post->groupname;
            $create->save();
        } elseif (strtolower(Request::$method === 'post')) {
            Message::instance()->set('Could not delete role.', Message::ERROR);
        } else {
            Message::instance()->set('Bad request.');
        }
        
	$this->request->redirect("admin/groups/");
    }
   
}
