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
        $sz = isset($_GET['sz']) ? (int)$_GET['sz'] : null;
        if(isset($_GET['q'])) {
            $this->response = Sourcemap_QRencode::encode($_GET['q'], $sz);
        } else {
            header('HTTP/1.1 400 Bad Request');
            die("Parameter 'q' required.");
        }
    }
}
