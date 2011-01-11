<?php
class Controller_Services_Geocode extends Controller_Services {
    public function action_get() {
        if(isset($_GET['placename']) && !empty($_GET['placename'])) {
            $q = $_GET['placename'];
        } elseif(isset($_GET['ll'])) {
            // note: parameter 'll' is lat/lng, as the WGS84 datum specifies.
            // should we do something to avoid confusion here? (e.g. lat=0&lng=0)?
            if(!preg_match('/((-|\+)?[\d]{1,3}(\.\d+)?,?){2}/', $_GET['ll']))
                return $this->_bad_request('Invalid lat/lng.');
            list($lat, $lng) = explode(',', $_GET['ll']);
            $q = new Sourcemap_Proj_Point($lng, $lat);
        }
        $this->response = (object)array(
            'results' => Sourcemap_Geocoder::geocode($q)
        );
    }
}
