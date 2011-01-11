<?php
class Controller_Services_Csv2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        $defaults = array(
            'headers' => 'true', 'latcol' => null,
            'loncol' => null
        );
        if(is_object($posted) && isset($posted->csv_file) && $posted->csv_file instanceof Sourcemap_Upload) {
            foreach($defaults as $k => $v) $$k = isset($posted->{$k}) ? $posted->{$k} : $v;
            $csv = Sourcemap_Csv::parse($posted->csv_file->get_contents());
            $data = array();
            if($csv) {
                $headers = array_shift($csv);
                foreach($csv as $ri => $row) {
                    $record = array();
                    foreach($headers as $hi => $k) {
                        if(isset($row[$hi]))
                            $record[$k] = $row[$hi];
                    }
                    if($record)
                        $data[] = $record;
                }
            }
            $headers = isset($posted->headers) ? (boolean)$posted->headers : $defaults['headers'];
            $latcol = isset($posted->latcol) ? $posted->latcol : $defaults['latcol'];
            $loncol = isset($posted->loncol) ? $posted->loncol : $defaults['loncol'];
            if($headers) {
                if(is_null($latcol) || is_null($loncol)) {
                    foreach($headers as $i => $h) {
                        if(is_null($latcol) && preg_match('/^lat(itude)?$/i', $h)) {
                            $latcol = $i;
                        } elseif(is_null($loncol) && preg_match('/^(lng)|(lon(g(itude)?)?)$/i', $h)) {
                            $loncol = $i;
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
            $this->response = $data;
        } elseif(($posted = (object)$_POST) && isset($posted->csv)) {
            foreach($defaults as $k => $v) $$k = isset($posted->{$k}) ? $posted->{$k} : $v;
            $csv = $posted->csv;
            $this->response = $csv;
        } else {
            return $this->_bad_request('No C.S.V. uploaded or posted.');
        }
    }
}
