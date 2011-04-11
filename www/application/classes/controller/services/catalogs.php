<?php
class Controller_Services_Catalogs extends Sourcemap_Controller_Service {
    public function action_get($catnm) {
        try {
            $results = Sourcemap_Catalog::get($catnm, $_GET);
        } catch(Exception $e) {
            return $this->_internal_server_error('Catalog is broken: '.$e);
        }
        $this->response = $results;
    }
}
