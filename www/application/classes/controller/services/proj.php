<?php
class Controller_Services_Proj extends Sourcemap_Controller_Service {
    public function action_get() {
        header('Content-Type: text/plain');
        #print_r(new Sourcemap_Proj_Projection('EPSG:900913'));
        #print_r(new Sourcemap_Proj_Projection('EPSG:4326'));
        #print_r(Sourcemap_Proj_Point::parse_coords('x=1,y=2'));
        print_r(Sourcemap_Proj::transform('WGS84', 'EPSG:900913', new Sourcemap_Proj_Point(-76.640625, 49.921875)));
    }
}
