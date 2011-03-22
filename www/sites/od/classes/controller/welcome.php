<?php
class Controller_Welcome extends Controller {
    public function action_index() {
        $this->request->redirect('sites/od/index.html');
    }
}
