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
        $items = 20;
        $offset = ($items * ($page - 1));
        $count = $user->count_all();
        $pagination = Pagination::factory(array(
                          'current_page' => array(
                              'source' => 'query_string', 
                              'key' => 'page'
                              ),
                          'total_items' => $user->count_all(),
                          'items_per_page' => $items,
                          ));
        $this->template->users = $user->order_by('username', 'ASC')
        ->limit($pagination->items_per_page)
        ->offset($pagination->offset)
        ->find_all()->as_array(null, array('id', 'username', 'email'));
        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        
        $user_count = $user->count_all();
        
        Breadcrumbs::instance()->add('Management', 'admin/')
        ->add('Users', 'admin/users');
    }
    
    
    public function action_details($id) {    
    
        $this->template = View::factory('admin/users/details');
        $user = ORM::factory('user', $id);
        
        $get_last_login = $user->last_login;
        $last_login =  date("F j, Y, g:i a", $get_last_login);


        $roles = array();
        foreach($user->roles->find_all()->as_array() as $i => $role) {
            $roles[] = $role->as_array();
        }        
        
        $all_roles = ORM::factory('role')->find_all()->as_array('id', array('id', 'name'));
        
        $members = array();
        foreach($user->groups->find_all()->as_array() as $i => $usergroup) {
            $members[] = $usergroup->as_array();
        }
        
        $owners = array();
        foreach($user->owned_groups->find_all()->as_array() as $i => $usergroup) {
            $owners[] = $usergroup->as_array();
        }

        $apikeys = $user->apikeys->find_all()->as_array('apikey', true);
        
        $this->template->user = $user;
        $this->template->roles = $roles;
        $this->template->all_roles = $all_roles;
        $this->template->members = $members;
        $this->template->owners = $owners;
        $this->template->last_login = $last_login;
        $this->template->apikeys = $apikeys;
                
        // this is to reset the password
        // todo: move this.
        $post = Validate::factory($_POST);
        $post->rule('email', 'not_empty')
            ->rule('password', 'not_empty')
            ->rule('password', 'max_length', array(16))
            ->rule('password', 'min_length', array(6))
            ->rule('confirmpassword', 'not_empty')
            ->rule('confirmpassword', 'max_length', array(16))
            ->rule('confirmpassword', 'min_length', array(6))
            ->rule('email', 'validate::email')
            ->filter(true, 'trim');
        
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            
            if($post->password == $post->confirmpassword) {
                $user_row = ORM::factory('user')
                ->where('email', '=', $post->email)
                ->find();
                
                $user_row->password = $post->password;

                try {
                $user_row->save();
                Message::instance()->set('Password changed successfully!');
                } catch (Exception $e) {
                Message::instance()->set('Password reset failed!');
                }
                
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


    public function action_create() {

        $post = Validate::factory($_POST);
        $post->rule('email', 'not_empty')
            ->rule('username', 'not_empty')
            ->rule('email', 'validate::email')
            ->filter('username', 'strip_tags')
            ->filter(true, 'trim');
            
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            
            $password = text::random($type = 'alnum', $length = 6);
            $create = ORM::factory('user');
            $create->username = $post->username;                
            $create->email = $post->email;
            $create->password = $password;
            
            try {
                $create->save();
            } catch (Exception $e) {
                Message::instance()->set('Could not create user. User already exists.');
                $this->request->redirect("admin/users/");
            }
                            
            //add a default login role when a new user is created
            $role = ORM::factory('role', array('name' => 'login'));
            
            try {
                $create->add('roles', $role)->save();
            } catch(Exception $e) {
                Message::instance()->set('Could not create user login role.');
                $this->request->redirect("admin/users/");
            }
            
        } elseif (strtolower(Request::$method === 'post')) {
            Message::instance()->set('Could not delete role.', Message::ERROR);
        }else {
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
            
            try {
                $user = ORM::factory('user', $id)->remove('roles', $role_id)->save();
            } catch (Exception $e) {
                Message::instance()->set('Could not delete role.', Message::ERROR);
            }
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
            if(!$user->loaded()) {
                Message::instance()->set('That user does not exist.');
                $this->request->redirect('admin/users');
            }
            $role = ORM::factory('role', array('name' => $post->addrole));
            try {
                $user->add('roles', $role)->save();
            } catch (Exception $e) {
                Message::instance()->set('Could not add the role.');
            }
        }  
        $this->request->redirect("admin/users/".$id);
    }


    public function action_delete_user($id) {
        $user = ORM::factory('user', $id);
        try {
            $user->delete();
        } catch (Exception $e) {
            Message::instance()->set('Could not delete the user.');
        }
        $this->request->redirect("admin/users/");
    }

    public function action_flags($id) {
        $user = ORM::factory('user', $id);
        if(!$user->loaded()) {
            Message::instance()->set('That user does not exist.');
            $this->request->redirect('admin/users');
        }
        $flagkeys = array(
            'verified' => Sourcemap::VERIFIED
        );
        foreach($flagkeys as $k => $v) {
            if(isset($_POST[$k]))
                $user->flags |= $v;
            else
                $user->flags &= ~$v;
        }
        $user->save();
        Message::instance()->set('Flags updated.', Message::SUCCESS);
        $this->request->redirect('admin/users/'.$user->id);
    }
}
