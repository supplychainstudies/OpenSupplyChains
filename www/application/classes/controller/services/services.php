<?php
class Controller_Services_Services extends Sourcemap_Controller_Service {
    public function action_get() {
        $this->response = array(
            'search', 'supplychains'
        );
    }
}
