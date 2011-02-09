<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


class Controller_Admin_Users extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/users/list';

    public function action_index() {
        $user = ORM::factory('user');
        $page = max($this->request->param('page'), 1);
        $items = 5;
        $offset = ($items * ($page - 1));
        $count = $user->count_all();
        $pagination = Pagination::factory(
            array('current_page' => array('source' => 'query_string', 'key' => 'page'),
          'total_items' => $user->count_all(),
          'items_per_page' => $items,
                ));
        $this->template->users = $user->order_by('username', 'ASC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()->as_array(null, array('id', 'username', 'email'));
        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Users', 'admin/users');
    }



    public function action_details($id) {

        $this->template = View::factory('admin/users/details');
        $user = ORM::factory('user', $id);
        $roles = array();
        foreach($user->roles->find_all()->as_array() as $i => $role) {
            $roles[] = $role->as_array();
        }


        $all_roles = ORM::factory('role')->find_all()->as_array('id', 
                                array('id', 'name')
        );

        $members = array();
        foreach($user->groups->find_all()->as_array() as $i => $usergroup) {
            $members[] = $usergroup->as_array();
        }

        $owners = array();
        foreach($user->owned_groups->find_all()->as_array() as $i => $usergroup) {
            $owners[] = $usergroup->as_array();
        }

        $this->template->user = $user;
        $this->template->roles = $roles;
        $this->template->all_roles = $all_roles;
        $this->template->members = $members;
        $this->template->owners = $owners;


        // this is to reset the password
        $post = Validate::factory($_POST);
        $post->rule('email', 'not_empty')
            ->rule('password', 'not_empty')
            ->rule('password', 'max_length', array(16))
            ->rule('confirmpassword', 'not_empty')
            ->rule('confirmpassword', 'max_length', array(16))
	    ->rule('email', 'validate::email')
            ->filter(true, 'trim');

        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();

            if($post->password == $post->confirmpassword) {
                $user_row = ORM::factory('user')
                    ->where('email', '=', $post->email)
                    ->find();

                $user_row->password = $post->password;
                $user_row->save();
                Message::instance()->set('Password changed successfully!');
                $this->request->redirect("admin/users/".$id);
            } else {
                Message::instance()->set('Please enter again - passwords did not match.', Message::ERROR);
            }
        } elseif(strtolower(Request::$method) === 'post') {
            Message::instance()->set('Invalid Password Reset.', Message::ERROR);
        }
	
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Users', 'admin/users')
            ->add(ucwords($user->username), 'admin/users/'.$user->id);
    }


    function _genpassword($len = 6) {    
        $password = '';
        for($i=0; $i<$len; $i++)
            $password .= chr(rand(0, 25) + ord('a'));
        return $password;
    }


    public function action_create() {
        $post = Validate::factory($_POST);
        $post->rule('email', 'not_empty')
            ->rule('username', 'not_empty')
	    ->rule('email', 'validate::email')
            ->filter(true, 'trim');
	    
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();

            $password = $this->_genpassword();
            $create = ORM::factory('user');
	    $all_users = $create->find_all()->as_array(null, 'username');
	    $all_emails = $create->find_all()->as_array(null, 'email');
	    if(!in_array($post->username, $all_users) && !in_array($post->email, $all_emails)) {
		$create->username = $post->username;		
		$create->email = $post->email;
		$create->password = $password;
		$create->save();
	    } else {
		Message::instance()->set('User already exists.');
	    }
        } elseif (strtolower(Request::$method === 'post')) {
            Message::instance()->set('Could not delete role.', Message::ERROR);
        } else {
            Message::instance()->set('Bad request.');
        }

        $this->request->redirect("admin/users/");
    }

    public function action_delete_role($id) {
        $post = Validate::factory($_POST);
        $post->rule('role', 'not_empty')
            ->filter(true, 'trim');
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            $role = $post->role;  
            $role_id = ORM::factory('role', array('name' => $role));

            $user = ORM::factory('user', $id)->remove('roles', $role_id)->save();

        } elseif(strtolower(Request::$method === 'post')) {
            Message::instance()->set('Could not delete role.', Message::ERROR);
        } else {
            Message::instance()->set('Bad request.');
        }

        $this->request->redirect("admin/users/".$id);
    }

    public function action_add_role($id) {
        $check =  false;
        $post = Validate::factory($_POST);
        $post->rule('addrole', 'not_empty')->filter(true, 'trim');
        if($post->check()) {
            $post = (object)$post->as_array();
            $user = ORM::factory('user', $id);
            $roles = array();
            foreach($user->roles->find_all()->as_array() as $i => $role) {
                $roles[] = $role->as_array();
            }
            $role_added = $post->addrole;

            $roleid = ORM::factory('role', array('name' => $role_added));


            //check if the role already exists, if not add the new role
            foreach($roles as $i => $k) {
                if($roles[$i]['name'] == $role_added){
                    $check = true;
                    break;
                }
            }
            if ($check == false || (count($roles)<0)) {
                $user = ORM::factory('user', $id)->add('roles', $roleid)->save();
            }  
        } else {
            Message::instance()->set('Please try again.', Message::ERROR);
        }
        $this->request->redirect("admin/users/".$id);
    }
}
