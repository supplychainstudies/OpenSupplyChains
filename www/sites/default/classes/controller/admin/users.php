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
        $items = 5;
        $offset = ($items * ($page - 1));
        $count = $user->count_all();
        $pagination = Pagination::factory(
                array('current_page' => array('source' => 'query_string', 'key' => 'page'),
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


          $all_roles = ORM::factory('role')->find_all()->as_array('id', array('id', 'name'));

          $this->template->main_content->user = $user;
          $this->template->main_content->roles = $roles;
          $this->template->main_content->all_roles = $all_roles;


          // this is to reset the password
          $post = Validate::factory($_POST);
          $post->rule('email', 'not_empty')
              ->rule('password', 'not_empty')
              ->rule('password', 'max_length', array(16))
              ->rule('confirmpassword', 'not_empty')
              ->rule('confirmpassword', 'max_length', array(16))
              ->filter(true, 'trim');	

          if($post->check()) {

              $post = (object)$post->as_array();

              if($post->password == $post->confirmpassword) {
                  $user_row = ORM::factory('user')
                      ->where('email', '=', $post->email)
                      ->find();

                  $user_row->password = $post->password;
                  $user_row->save();
                  $this->template->main_content->reset = true;

              } else {
                  Message::instance()->set('Please enter again - passwords did not match.', Message::ERROR);
              }
          } else {
              Message::instance()->set('Invalid Password Reset.', Message::ERROR);
          }

      }

      public function action_delete($id) {
          $post = Validate::factory($_POST);
          $post->rule('role', 'not_empty')
              ->filter(true, 'trim');
          if($post->check()) {
              $post = (object)$post->as_array();
              $role = $post->role;  
              $role_id = ORM::factory('role', array('name' => $role));

              $user = ORM::factory('user', $id)->remove('roles', $role_id)->save();

          } else {
              Message::instance()->set('Could not delete role.', Message::ERROR);
          }

          $this->request->redirect("admin/users/single/".$id);
      }

      public function action_add($id) {
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
          $this->request->redirect("admin/users/single/".$id);
      }
}
