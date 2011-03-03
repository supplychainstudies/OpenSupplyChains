<?php
class Controller_Map extends Sourcemap_Controller_Layout {
    
    public $layout = 'map';
    public $template = 'map/view';

    protected function _match_alias($alias) {
        $found = ORM::factory('supplychain_alias')
            ->where('site', '=', Kohana::config('sourcemap.site'))
            ->where('alias', '=', $alias)
            ->find_all()->as_array('alias', 'supplychain_id');
        $supplychain_id = $found ? $found[$alias] : -1;
        return $supplychain_id;
    }
    
    public function action_view($supplychain_id) {
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                $this->template->supplychain_id = $supplychain->id;
                $this->layout->scripts = array(
                    'modernizr', 'less', 'map-view', 'sourcemap-core', 
                    'sourcemap-template', 'sourcemap-working'
                );
                $this->layout->styles = array(
                    'assets/styles/style.css', 
                    'assets/styles/sourcemap.less?v=2'
                );
            } else {
                $this->request->status = 403;
                $this->layout = View::factory('layout/error');
                $this->template = View::factory('error');
                $this->template->error_message = 'This map is private.';
            }
        } else {
            $this->request->status = 404;
            $this->layout = View::factory('layout/error');
            $this->template = View::factory('error');
            $this->template->error_message = 'That map could not be found.';
        }
    }

    public function action_static($supplychain_id) {
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                header('Content-Type: image/png');
                $cache_file = Kohana::$cache_dir.'static-map-'.$supplychain_id.'.png';
                if(file_exists($cache_file)) {
                    print file_get_contents($cache_file);
                    header('X-Cache-Hit: true');
                } else {
                    $img_data = CloudMade_StaticMap::get_image($supplychain->kitchen_sink($supplychain_id));
                    file_put_contents($cache_file, $img_data);
                    print $img_data;
                }
                exit;
            } else {
                $this->request->status = 403;
                $this->layout = View::factory('layout/error');
                $this->template = View::factory('error');
                $this->template->error_message = 'This map is private.';
            }
        } else {
            $this->request->status = 404;
            $this->layout = View::factory('layout/error');
            $this->template = View::factory('error');
            $this->template->error_message = 'That map could not be found.';
        }
    }

    public function action_embed($supplychain_id) {
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                $this->layout = View::factory('layout/embed');
                $this->template = View::factory('map/embed');
                $this->layout->supplychain_id = $supplychain_id;
                $this->layout->scripts = array(
                    'sourcemap-embed'
                );
                $this->layout->styles = array(
                    'sites/default/assets/styles/style.css',
                    'sites/default/assets/styles/sourcemap.less?v=2'
                );
                $params = array(
                    'tour' => 'yes', 'tour_start_delay' => 7,
                    'tour_interval' => 5, 'banner' => false,
                    'geoloc' => true
                );
                foreach($params as $k => $v) 
                    if(isset($_GET[$k])) 
                        $params[$k] = $_GET[$k];
                $v = Validate::factory($params);
                $v->rule('tour', 'regex', array('/yes|no/i'))
                    ->rule('tour_start_delay', 'numeric')
                    ->rule('tour_start_delay', 'range', array(0, 300))
                    ->rule('tour_interval', 'numeric')
                    ->rule('tour_interval', 'range', array(1, 15))
                    ->rule('geoloc', 'not_empty');
                if($v->check()) {
                    $params = $v->as_array();
                    $params['tour_start_delay'] = (int)$params['tour_start_delay'];
                    $params['tour_interval'] = (int)$params['tour_interval'];
                    $params['tour'] = 
                        strtolower(trim($params['tour'])) === 'yes' ? true : false;
                    if($params['geoloc']) {
                        $params['iploc'] = false;
                        if(isset($_SERVER['REMOTE_ADDR'])) {
                            $_SERVER['REMOTE_ADDR'] = '18.9.22.69';
                            $params['iploc'] = Sourcemap_Ip::find_ip($_SERVER['REMOTE_ADDR']);
                        }
                    }
                    $this->layout->embed_params = $params;
                } else {
                    $this->request->status = 400;
                    $this->layout = View::factory('layout/embed');
                    $this->template = View::factory('error');
                    $this->template->error_message = 'Bad params: '.http_build_query($params).'.';
                }
            } else {
                $this->request->status = 403;
                $this->layout = View::factory('layout/embed');
                $this->template = View::factory('error');
                $this->template->error_message = 'This map is private.';
            }

        } else {
            $this->request->status = 404;
            $this->layout = View::factory('layout/error');
            $this->template = View::factory('error');
            $this->template->error_message = 'That map could not be found.';
        }
    }
}
