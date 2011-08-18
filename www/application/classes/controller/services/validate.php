<?php
class Controller_Services_Validate extends Sourcemap_Controller_Service {
    public function action_post($p=false) {
        if($p === false) {
            return $this->_bad_request();
        }

        $f = Sourcemap_Form::load($p);

        if($f) {
            if($f->validate((array)$this->request->posted_data)) {
                $this->response = true;
            } else {
                $this->response = (object)$f->errors();
            }
        } else {
            return $this->_not_found('Form does not exist.');
        }
    }
}
