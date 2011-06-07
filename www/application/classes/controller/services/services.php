<?php
class Controller_Services_Services extends Sourcemap_Controller_Service {
    public function action_get() {
        $this->response = array(
            'services' => array(
                'search', 'supplychains'
            ),
            'you' => $this->get_current_user() ? $this->get_current_user()->username : false
        );
    }
}
