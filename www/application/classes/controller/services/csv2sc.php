<?php
class Controller_Services_Csv2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        $defaults = array(
            'headers' => 'true', 'latcol' => null,
            'loncol' => null
        );
        if(is_object($posted) && isset($posted->csv_file) && $posted->csv_file instanceof Sourcemap_Upload) {
            $csv = Sourcemap_Csv::parse($posted->csv_file->get_contents());
        } elseif(($posted = (object)$_POST) && isset($posted->csv)) {
            $csv = $posted->csv;
        } else {
            return $this->_bad_request('No C.S.V. uploaded or posted.');
        }
        $headers = isset($posted->headers) ? (boolean)$posted->headers : $defaults['headers'];
        $latcol = isset($posted->latcol) ? $posted->latcol : $defaults['latcol'];
        $loncol = isset($posted->loncol) ? $posted->loncol : $defaults['loncol'];

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
                    }
                }
                if(is_null($latcol) || is_null($loncol)) {
                    return $this->_bad_request('Missing lat/lon column index.');
                }
            }
        } else {
            if(is_null($latcol) || is_null($latcol)) {
                return $this->_bad_request(
                    'Headers or lat/lon column indices required.'
                );    
            }
        }
        $sc = new stdClass();
        $sc->hops = array();
        $sc->stops = array();
        foreach($data as $i => $record) {
            if(!isset($record[$latcol], $record[$loncol]))
                return $this->_bad_request('Missing lat/lon field (record #'.($i+1).').');
            $new_stop = array(
                'attributes' => array()
            );
            foreach($record as $k => $v) {
                if($k == $latcol || $k == $loncol)
                    continue;
                $new_stop['attributes'][$k] = $v;
            }
            $from_pt = new Sourcemap_Proj_Point($record[$loncol], $record[$latcol]);
            $new_stop['geometry'] = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $from_pt)->toGeometry();
            $sc->stops[] = (object)$new_stop;
        }
        $this->response = $sc;
    }
}
