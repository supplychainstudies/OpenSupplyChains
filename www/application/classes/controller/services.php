<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
 class Controller_Services extends Sourcemap_Controller_Service {
    public function action_get() {
        $this->response = array(
            'message' => 'There\'s not much to see here right now.'
        );
    }
 }
