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

    public function __construct($request) {
        parent::__construct($request);
        $current_user_id = Auth::instance()->get_user();
        if($current_user_id) {
            $current_user = ORM::factory('user', $current_user_id);
        } else {
            $current_user =  false;
        }
        if(!$current_user) {
            Message::instance()->set(
                'You\'re not allowed to access the management dashboard.', Message::ERROR
            );
            $this->request->redirect('error');
        }
    }

    public function action_index() {
        $this->layout->page_title = 'Management Dashboard';
        Breadcrumbs::instance()->add('Management', 'admin/');
    }
}
