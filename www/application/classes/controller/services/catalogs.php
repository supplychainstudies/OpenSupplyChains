<?php
class Controller_Services_Catalogs extends Sourcemap_Controller_Service {
    public function action_get($nm) {
        $results = Sourcemap_Catalog::get('osi', array('name' => 'ORANGES'));
        $this->response = $results;
    }
}
