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
