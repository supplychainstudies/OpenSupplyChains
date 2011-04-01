<?php
class Controller_Services_Shorten extends Controller_Services {
    public function action_get() {        
        if(isset($_GET['url'])) {
            $this->response = Sourcemap_Bitly::shorten($_GET['url']);
            if(!$this->response) {
                return $this->_bad_request("Request failed.");
            }
        } else {
            return $this->_bad_request("Url required.");
        }
    }
}
