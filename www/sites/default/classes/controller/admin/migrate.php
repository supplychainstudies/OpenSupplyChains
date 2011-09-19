<?php
class Controller_Admin_Migrate extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/migrate';
  
    public function action_index() {
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Migration', 'admin/migrate');
        if(strtolower(Request::$method) === 'post') {
            return $this->_do_migrate();
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

    public function _do_migrate() {
        
    }
}
