<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Services_Users extends Sourcemap_Controller_Service {
    public function action_get($username) {
        $user = ORM::factory('user')->where('username', '=', $username)->find_all();
        if($user->count() > 0) {
            #sleep(3); // todo: throttle...
            $this->response = $user->get('id');
        } else {
            $this->_not_found('User not found.');
        }
    }
}

