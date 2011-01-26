<?php
class Controller_Services_Csv2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        $defaults = array(
            'headers' => 'true', 'latcol' => null,
            'loncol' => null, 'addresscol' => null
        );
        if(is_object($posted) && isset($posted->stop_file) && $posted->stop_file instanceof Sourcemap_Upload) {
            $csv = Sourcemap_Csv::parse($posted->stop_file->get_contents());
        } elseif(($posted = (object)$_POST) && isset($posted->stop_csv)) {
            $csv = $posted->stop_csv;
        } else {
            return $this->_bad_request('No C.S.V. uploaded or posted.');
        }
        $headers = isset($posted->headers) ? (boolean)$posted->headers : $defaults['headers'];
        $latcol = isset($posted->latcol) ? $posted->latcol : $defaults['latcol'];
        $loncol = isset($posted->loncol) ? $posted->loncol : $defaults['loncol'];
        $addresscol = isset($posted->addresscol) ? $posted->addresscol : $defaults['addresscol'];

        $data = array();
        if($csv) {
            if($headers) $headers = array_shift($csv);
            foreach($csv as $ri => $row) {
                if($headers && is_array($headers)) {
                    $record = array();
                    foreach($headers as $hi => $k) {
                        if(isset($row[$hi]))
                            $record[$k] = $row[$hi];
                    }
                } else $record = array_slice($row, 0, 2);
                if($record)
                    $data[] = $record;
            }
        }
        if($headers) {
            if(is_null($latcol) || is_null($loncol)) {
                foreach($headers as $i => $h) {
                    if(is_null($latcol) && preg_match('/^lat(itude)?$/i', $h)) {
                        $latcol = $h;
                    } elseif(is_null($loncol) && preg_match('/^(lng)|(lon(g(itude)?)?)$/i', $h)) {
                        $loncol = $h;
                    } elseif($h == "address" || $h == "placename") {
                        $addresscol = $h;
                    }
                }
                if(is_null($latcol) || is_null($loncol)) {
                    $latcol = $loncol = null;
                    if(is_null($addresscol))
                        return $this->_bad_request('Missing lat/lon or address column index.');
                }
            }
        } else {
            if(is_null($latcol) || is_null($loncol)) {
                $latcol = $loncol = null;
                if(is_null($addresscol))
                    return $this->_bad_request(
                        'Headers or lat/lon column indices required.'
                    );    
            }
        }
        $sc = new stdClass();
        $sc->stops = $this->_make_stops($data, array(
            'latcol' => $latcol, 'loncol' => $loncol, 'addresscol' => $addresscol
        ));
        $hop_csv = false;        
        if(is_object($posted) && isset($posted->hop_file) && $posted->hop_file instanceof Sourcemap_Upload) {
            $hop_csv = Sourcemap_Csv::parse($posted->hop_file->get_contents());
        } elseif(($posted = (object)$_POST) && isset($posted->stop_csv)) {
            $hop_csv = $posted->hop_csv;
        }
        $sc->hops = $this->_make_hops(array());

        $sc->attributes = array();
        $this->response = (object)array('supplychain' => $sc);
    }

    protected function _make_stops($data, $options = array()) {
        extract($options);
        $stops = array();
        foreach($data as $i => $record) {
            if(is_null($addresscol)) {
                if(!isset($record[$latcol], $record[$loncol]))
                    return $this->_bad_request('Missing lat/lon field (record #'.($i+1).').');
            } else {
                if(!isset($record[$addresscol]))
                    return $this->_bad_request('Missing address field (record #'.($i+1).').');
            }
            $new_stop = array(
                'id' => "stop-$i",
                'attributes' => array()
            );
            $lat = null;
            $lon = null;
            foreach($record as $k => $v) {
                if($k == $latcol || $k == $loncol) {
                    if($k == $latcol) $lat = $v;
                    else $lon = $v;
                    continue;
                } elseif($k == $addresscol) {
                    if($results = Sourcemap_Geocoder::geocode($v)) {
                        $result = $results[0];
                        $lat = $result->lat;
                        $lon = $result->lng;
                    } else {
                        return $this->_internal_server_error('Could not geocode: "'.$v.'".');
                    }
                }
                $new_stop['attributes'][$k] = $v;
            }
            if(is_null($lon) || is_null($lat)) return $this->_internal_server_error('No lat/lon.');
            $from_pt = new Sourcemap_Proj_Point($lon, $lat);
            $new_stop['geometry'] = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $from_pt)->toGeometry();
            $stops[] = (object)$new_stop;
        }
        return $stops;
    }

    protected function _make_hops($data) {
        return array();
    }
}
