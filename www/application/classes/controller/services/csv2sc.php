<?php
class Controller_Services_Csv2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        if(is_object($posted) && isset($posted->stop_file) && $posted->stop_file instanceof Sourcemap_Upload) {
            $stop_csv = $posted->stop_file->get_contents();
            if(isset($posted->hop_file) && $posted->hop_file instanceof Sourcemap_Upload)
                $hop_csv = $posted->hop_file->get_contents;
        } elseif(($posted = (object)$_POST) && isset($posted->stop_csv)) {
            $stop_csv = $posted->stop_csv;
            if(isset($posted->hop_csv))
                $hop_csv = $posted->hop_csv;
        } else {
            return $this->_bad_request('No C.S.V. uploaded or posted.');
        }

        $sc = Sourcemap_Import_Csv::csv2sc($stop_csv, $hop_csv, (array)$posted);
        $this->response = (object)array('supplychain' => $sc);
    }
}
