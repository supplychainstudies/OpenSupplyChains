<?php
class Controller_Services_Proj extends Sourcemap_Controller_Service {
    public function action_get() {
        print_r(new Sourcemap_Proj_Projection('EPSG:900913'));
        print_r(new Sourcemap_Proj_Projection('EPSG:4326'));
    }
}
