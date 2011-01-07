<?php
class Controller_Services_Csv2Sc extends Sourcemap_Controller_Service {
    public function action_post() {
        $posted = $this->request->posted_data;
        if(isset($posted['csv_file']) && $posted['csv_file'] instanceof Sourcemap_Upload) {
            $csv = Sourcemap_Csv::parse($posted['csv_file']->get_contents());
        } else {
            $csv = $posted;
        }
        die(print_r($csv, true));
        $this->response = $csv;
    }
}
