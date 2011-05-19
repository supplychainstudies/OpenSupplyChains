<?php
class Controller_Services_Search extends Sourcemap_Controller_Service {
    public function action_get() {
        $t = Request::instance()->param('id', 'simple');
        try {
            $this->response = Sourcemap_Search::find($t, $_GET);
        } catch(Exception $e) {
            $this->_not_found('What are you trying to search?');
        }
    }
}
