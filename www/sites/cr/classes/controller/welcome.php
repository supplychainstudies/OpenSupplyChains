<?php
class Controller_Welcome extends Controller {
    public function action_index() {
        $this->request->redirect('sites/cr/index.html');
    }
}
