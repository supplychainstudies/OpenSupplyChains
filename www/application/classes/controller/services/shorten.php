<?php
class Controller_Services_Shorten extends Controller_Services {
    public function action_get() {        
        if(isset($_GET['url'])) {
            $this->response = Sourcemap_Bitly::shorten($_GET['url']);
            if(!$this->response) {
                header('HTTP/1.1 400 Bad Request');
                die("Request failed.");
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            die("Url required.");
        }
    }
}
