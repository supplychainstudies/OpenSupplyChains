<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

class Controller_Tools_Import_Google extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'tools/import/google';

    public function before() {
        if(!Auth::instance()->get_user()) {
            Message::instance()->set('You must be signed in to use the importer.');
            $this->request->redirect('/auth?next=/tools/import/google');
        }
        $current_user = Auth::instance()->get_user();
        $import_role = ORM::factory('role')->where('name', '=', 'import')->find();
        $admin_role = ORM::factory('role')->where('name', '=', 'admin')->find();
        if($current_user->has('roles', $import_role) || $current_user->has('roles', $admin_role)) {
            // pass
        } else {
            Message::instance()->set('You don\'t have access to the Google Docs importer.');
            $this->request->redirect('/home');
        }
        parent::before();
    }

    public function action_index() {
        if(Session::instance()->get('g_oauth_access_token')) {
            $this->request->redirect('/tools/import/google/list');
        }
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $oauth->_req_token_callback = Url::site('/tools/import/google/auth', true);
        $auth_token = $oauth->get_req_token();
        if(!$auth_token) throw new Exception('Could not obtain auth token.');
        $secret = $auth_token['oauth_token_secret'];
        Session::instance()->set('g_oauth_token_secret', $secret);
        unset($auth_token['oauth_token_secret']);
        $this->request->redirect(
            Google_Oauth::OAUTH_BASE.Google_Oauth::OAUTH_AUTHTOKEN.
                Url::query($auth_token)
        );
    }

    
    public function action_auth() {

        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template', 
            'sourcemap-working', 'sourcemap-upload'
        );
        $this->layout->styles = array(
            'assets/styles/style.css', 
            'assets/styles/sourcemap.less'
        );

        if(isset($_GET['oauth_token'], $_GET['oauth_verifier']) && ($secret = Session::instance()->get('g_oauth_token_secret'))) {
            $auth_tok = array(
                'oauth_token' => $_GET['oauth_token'],
                'oauth_verifier' => $_GET['oauth_verifier'],
                'oauth_token_secret' => $secret
            );
        } else {
            Message::instance()->set('Invalid OAuth token or identifier. Try again.');
            $this->redirect('/tools/import/google/');
        }
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $acc_token = $oauth->get_acc_token($auth_tok);
        Session::instance()->set('g_oauth_access_token', $acc_token);
        $this->request->redirect('/tools/import/google/list');
    }

    public function action_list() {
        if(!($acc_token = Session::instance()->get('g_oauth_access_token'))) {
            Message::instance()->set('You haven\'t given us permission to fetch spreadsheets.');
            $this->request->redirect('/tools/import/google/');
        }
        $list = Google_Spreadsheets::get_list($acc_token);
        $this->template = View::factory('tools/import/google/list');
        $this->template->list = $list;
    }

    public function action_import() {
        if(!($acc_token = Session::instance()->get('g_oauth_access_token'))) {
            Message::instance()->set('You haven\'t given us permission to fetch spreadsheets.');
            $this->request->redirect('/tools/import/google/');
        }
        if(Request::$method !== 'POST') {
            Message::instance()->set('Please choose a spreadsheet to import.');
            $this->request->redirect('/tools/import/google/list');
        }
        // TODO: validation
        if(!isset($_POST['k'], $_POST['stops-wsid'])) {
            Message::instance()->set('Spreadsheet key and worksheet id required.');
            $this->request->redirect('/tools/import/google/list');
        }
        $csv = Sourcemap_Csv::arr2csv(
            Google_Spreadsheets::get_worksheet_cells(
                $acc_token, $_POST['k'], $_POST['stops-wsid']
            )
        );
        if($csv && isset($_POST['hops-wsid']) && $_POST['hops-wsid']) {
            $hops_csv = Sourcemap_Csv::arr2csv(
                Google_Spreadsheets::get_worksheet_cells(
                    $acc_token, $_POST['k'], $_POST['hops-wsid']
                )
            );
        } else $hops_csv = null;
        $new_sc = Sourcemap_Import_Csv::csv2sc($csv, $hops_csv, array('headers' => true));
        if(isset($_POST['replace-into']) && $_POST['replace-into']) {
            $exists = ORM::factory('supplychain')->where('id', '=', $_POST['replace-into'])->find();
            if($exists && $exists->user_id == Auth::instance()->get_user()->id) {
                $replace_into = $exists->id;
            } else {
                Message::instance()->set('The supplychain you tried to replace is invalid.');
                $this->request->redirect('/tools/import/google/worksheets/?k='.$_POST['k']);
            }
        } else {
            $replace_into = null;
        }
        try {
            $new_sc->user_id = Auth::instance()->get_user()->id;
            $title = false;
            $title = isset($_POST['supplychain_name']) && $_POST['supplychain_name'] ? $_POST['supplychain_name'] : false;
            if($replace_into && !$title && ($keeptitle = $exists->attributes->where('key', 'in', array('title', 'name'))->find())) {
                $title = $keeptitle->value;
            }
            $new_sc->attributes = (object)array('title' => $title ? $title : 'Imported Sourcemap');
            $scid = ORM::factory('supplychain')->save_raw_supplychain($new_sc, $replace_into);
            $new_sc = ORM::factory('supplychain', $scid);
            
            //$new_sc->other_perms |= Sourcemap::READ;
            $public = isset($_POST['publish']) ? Sourcemap::READ : 0;
            $new_sc->other_perms = 0;
            if($public)
                $new_sc->other_perms |= $public;
            else
                $new_sc->other_perms &= ~Sourcemap::READ;

            $new_sc->save();
            Message::instance()->set('Your spreadsheet was imported.', Message::SUCCESS);
            $this->request->redirect('view/'.$scid);
        } catch(Exception $e) {
            Message::instance()->set('There was a problem importing your spreadsheet: '.$e);
            $this->request->redirect('/tools/import/google/worksheets/?k='.$_POST['k']);
        }
    }

    public function action_worksheets() {
        if(!($acc_token = Session::instance()->get('g_oauth_access_token'))) {
            Message::instance()->set('You haven\'t given us permission to fetch spreadsheets.');
            $this->request->redirect('/tools/import/google/');
        }
        if(!isset($_GET['k'])) {
            Message::instance()->set('Worksheet key required.');
            $this->request->redirect('/tools/import/google/list');
        }
        $worksheets = Google_Spreadsheets::get_sheet_worksheets($acc_token, $_GET['k']);
        if(!$worksheets) {
            Message::instance()->set('Could not fetch worksheets for that spreadsheet.');
            $this->request->redirect('/tools/import/google/list');
        }
        $this->template = View::factory('tools/import/google/worksheets');
        $this->template->worksheets = $worksheets;
        $this->template->spreadsheet_key = $_GET['k'];
        $this->template->user_supplychains = ORM::factory('supplychain')
            ->where('user_id', '=', Auth::instance()->get_user()->id)->find_all();
    }
  }
