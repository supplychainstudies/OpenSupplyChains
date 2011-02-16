<?php
class Controller_Tools_Import extends Sourcemap_Controller_Layout {
    
    public $layout = 'layout';
    public $template = 'tools/import/csv';

    public function action_index() {
        die('no!');
    }

    public function action_csv() {
        if(!Auth::instance()->get_user()) // todo: write method below.
            return $this->_forbidden('You must be logged in to use the import tool.');
        $this->layout->scripts = array(
            'sourcemap-core'
        );
        $this->layout->styles = array(
            'assets/styles/style.css',
            'assets/styles/sourcemap.less?v=2'
        );
        if(strtolower(Request::$method) === 'post') {
            $posted = (object)array_merge($_POST, Sourcemap_Upload::get_uploads());
            if(isset($posted->stop_file) && $posted->stop_file instanceof Sourcemap_Upload && $posted->stop_file->ok()) {
                $stop_csv = $posted->stop_file->get_contents();
                if(isset($posted->hop_file) && $posted->hop_file instanceof Sourcemap_Upload && $posted->hop_file->ok())
                    $hop_csv = $posted->hop_file->get_contents();
                else
                    $hop_csv = null;
                $sc = Sourcemap_Import_Csv::csv2sc($stop_csv, $hop_csv, $posted);
                $sc->user_id = Auth::instance()->get_user();
                $new_sc_id = ORM::factory('supplychain')->save_raw_supplychain($sc);
                if($new_sc_id)
                    Request::instance()->redirect('map/view/'.$new_sc_id);
                else
                    return $this->_internal_server_error('Could not save.');
            } else {
                return $this->_bad_request('Stop file required.');
            }
        }
    }
}
