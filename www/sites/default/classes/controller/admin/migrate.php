<?php
class Controller_Admin_Migrate extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/migrate';
  
    public function action_index() {
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Migration', 'admin/migrate');
        if(strtolower(Request::$method) === 'post') {
            if(isset($_POST['old_user_id']) && isset($_POST['new_user_id'])) {
                if(isset($_POST['confirm']))  {
                    $new_user = ORM::factory('user', $_POST['new_user_id']);
                    if(!$new_user->loaded()) {
                        Message::instance()->set('Invalid new user id.');
                        $this->request->redirect('admin/migrate');
                    }
                    if(Dotorg_Archive::instance()->migrate($_POST['old_user_id'], $new_user->id)) {
                        Message::instance()->set('Maps migrated.');
                        $this->request->redirect('admin/supplychains/');
                    } else {
                        Message::instance()->set('Problem migrating.');
                    }
                } else {
                    $this->template->old_user_id = $_POST['old_user_id'];
                    $this->template->new_user_id = $_POST['new_user_id'];
                    return;
                }
            } else {
                Message::instance()->set('Missing fields. Could not migrate.');
            }
            $this->request->redirect('admin/migrate');
        } else {
            if(isset($_GET['uid']) && $_GET['uid']) {
                $this->request->redirect('admin/migrate/'.$_GET['uid']);
            }
        }
    }

    public function action_details($uid) {
        $this->template = View::factory('admin/migrate/details');
        $arch = Dotorg_Archive::instance();
        if(is_numeric($uid)) {
            $oids = $arch->by_userid($uid);
        } else {
            $oids = $arch->by_username($uid);
        }
        $this->template->oids = $oids;
        $this->template->details = $arch->get_details($oids);
        $this->template->uid = $uid;
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Migration', 'admin/migrate')
            ->add($uid, 'admin/migrate/'.$uid);
    }
}
