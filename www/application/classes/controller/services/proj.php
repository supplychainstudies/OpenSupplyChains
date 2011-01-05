<?php
class Controller_Services_Proj extends Sourcemap_Controller_Service {
    public function action_get() {
        header('Content-Type: text/plain');
        #print_r(new Sourcemap_Proj_Projection('EPSG:900913'));
        #print_r(new Sourcemap_Proj_Projection('EPSG:4326'));
        #print_r(Sourcemap_Proj_Point::parse_coords('x=1,y=2'));
        $x = isset($_GET['x']) ? $_GET['x'] : null;
        $y = isset($_GET['y']) ? $_GET['y'] : null;
        $src = isset($_GET['src']) ? $_GET['src'] : 'WGS84';
        $dest = isset($_GET['dest']) ? $_GET['dest'] : 'WGS84';
        if($x == null || $y == null) {
            return $this->_bad_request('Invalid x/y combination.');
        }
        #$this->response = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', new Sourcemap_Proj_Point(-76.640625, 49.921875));
        $this->response = Sourcemap_Proj::transform($src, $dest, new Sourcemap_Proj_Point($x, $y));
    }
}
