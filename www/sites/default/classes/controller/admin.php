<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */


 class Controller_Admin extends Sourcemap_Controller_Layout {

    public $layout = 'admin';
    public $template = 'admin/dashboard';
    public $current_user = null;
    public $admin = null;

    public function __construct($request) {
        parent::__construct($request);
        $this->current_user = Auth::instance()->get_user();
	    $this->admin = ORM::factory('role')
            ->where('name', '=', 'admin')->find();
	    if($this->current_user && $this->current_user->has('roles', $this->admin)) {
            // pass
        } else {
            Message::instance()->set(
                'You\'re not allowed to access the management dashboard.', Message::ERROR
            );
            $this->request->redirect('auth/');
        }
    }

    public function action_index() {
        $this->layout->page_title = 'Management Dashboard';
        Breadcrumbs::instance()->add('Management', 'admin/');
    }
    
}
