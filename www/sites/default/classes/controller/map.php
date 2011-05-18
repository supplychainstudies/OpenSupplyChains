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
                $this->layout->scripts = array('map-view');
                $this->layout->styles = array(
                    'sites/default/assets/styles/reset.css', 
                    'assets/styles/general.less'
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
        $szs = array(
            "sml" => array(160, 105),
            "med" => array(250, 170),
            "lrg" => array(730, 400),
            "hug" => array(1024, 768)
        );
        $map_size = isset($_GET['sz']) && in_array($_GET['sz'], array_keys($szs)) ?
            $_GET['sz'] : 'sml';
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                header('Content-Type: image/png');
                $cache_key = "static-map-{$supplychain_id}-{$map_size}-png";
                $exists = Cache::instance()->get($cache_key);
                if($exists) {
                    header('X-Cache-Hit: true');
                    print $exists;
                } else {
                    $raw_sc = $supplychain->kitchen_sink($supplychain_id);
                    $sm = new Sourcemap_Map_Static($raw_sc);
                    $tiles_img = $sm->render();
                    $imgs = array();
                    foreach($szs as $k => $v) {
                        $resized = imagecreatetruecolor($v[0], $v[1]);
                        imagecopyresized($resized, $tiles_img, 0, 0, 0, 0, $v[0], $v[1], $sm->w, $sm->h);
                        ob_start();
                        imagepng($resized);
                        $resized = ob_get_contents();
                        ob_end_clean();
                        Cache::instance()->set("static-map-{$supplychain_id}-{$k}-png", $resized);
                        if($k === $map_size) print $resized;
                    }
                    ob_start();
                    imagepng($tiles_img);
                    $baseimg = ob_get_contents();
                    ob_end_clean();
                    Cache::instance()->set("static-map-{$supplychain_id}-base-png", $baseimg);
                    //Cache::instance()->get($cache_key, $img_data);
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
                    'sites/default/assets/styles/reset.css',
                    'assets/styles/embed.less'
                );
                $params = array(
                    'tour' => 'yes', 'tour_start_delay' => 7,
                    'tour_interval' => 5, 'banner' => 'yes',
                    'tileswitcher' => 'no', 'geoloc' => true, 
                    'downstream_sc' => null, 'tileset' => 'terrain',
                    'locate_user' => 'no'
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
                    ->rule('geoloc', 'not_empty')
                    ->rule('banner', 'regex', array('/yes|no/i'))
                    ->rule('tileswitcher', 'regex', array('/yes|no/i'))
                    ->rule('locate_user', 'regex', array('/yes|no/i'))
                    ->rule('tileset', 'regex', array('/terrain|satellite|cloudmade/i'))
                    ->rule('downstream_sc', 'numeric');
                if($v->check()) {
                    $params = $v->as_array();
                    $params['tour_start_delay'] = (int)$params['tour_start_delay'];
                    $params['tour_interval'] = (int)$params['tour_interval'];
                    $params['tour'] = 
                        strtolower(trim($params['tour'])) === 'yes' ? true : false;
                    $params['banner'] = 
                        strtolower(trim($params['banner'])) === 'yes' ? true : false;
                    $params['tileswitcher'] =
                        strtolower(trim($params['tileswitcher'])) === 'yes' ? true : false;
                    $params['locate_user'] =
                        strtolower(trim($params['locate_user'])) === 'yes' ? true : false;
                    $params['tileset'] = strtolower(trim($params['tileset']));
                    
                    if($params['geoloc']) {
                        $params['iploc'] = false;
                        if(isset($_SERVER['REMOTE_ADDR'])) {
                            //$_SERVER['REMOTE_ADDR'] = '128.59.48.24'; // ny, ny (columbia.edu)
                            $params['iploc'] = $iploc = Sourcemap_Ip::find_ip($_SERVER['REMOTE_ADDR']);
                            $iploc = $iploc ? $iploc[0] : null;
                            if($params['downstream_sc'] && $iploc) {
                                $pt = new Sourcemap_Proj_Point($iploc->longitude, $iploc->latitude);
                                $pt = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $pt);
                                $nearby = ORM::factory('stop', (int)$params['downstream_sc'])->nearby($pt, 3);
                                $params['downstream_nearby'] = $nearby;
                            }
                        }
                    }
                    $this->layout->embed_params = $params;
                } else {
                    $this->request->status = 400;
                    $this->layout = View::factory('layout/embed');
                    $this->template = View::factory('error');
                    $this->template->error_message = 'Bad parameters.';
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
