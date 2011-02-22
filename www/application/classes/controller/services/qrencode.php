<?php
class Controller_Services_Qrencode extends Controller_Services {

    public $_format = 'png';
    public $_default_format = 'png';
    public $_default_content_type = 'image/png';
    public $_content_types = array(
        'png' => 'image/png'
    );

    protected function _serialize($data, $format=null) {
        return $data;
    }

    public function action_get() {
        if(isset($_GET['s'])) {
            $this->response = Sourcemap_QRencode::encode($_GET['s']);
        } else {
            header('HTTP/1.1 400 Bad Request');
            die("Parameter 's' required.");
        }
    }
}
