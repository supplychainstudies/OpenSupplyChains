<?php
class Controller_Map extends Sourcemap_Controller_Layout {
    
    public $layout = 'map';
    public $template = 'map/view';

    public static function placeholder_image() {
        return Sourcemap::assets_path().'images/static-map-generating.png';
    }

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

    public function action_static($supplychain_id, $sz=null) {
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        $szdim = false;
        $valid_size = false;
        $image_sizes = Sourcemap_Map_Static::$image_sizes;
        $image_thumbs = Sourcemap_Map_Static::$image_thumbs;
        do {
            if(isset($image_sizes[$sz])) {
                $valid_size = true;
                $szdim = $image_sizes[$sz];
            } elseif($sz == 'o') {
                $valid_size = true;
                $szdim = array(1024,780);
            } else {
                foreach($image_thumbs as $tk => $tv) {
                    if("th-$tk" == $sz) {
                        $valid_size = true;
                        $szdim = $tv;
                        break;
                    }
                }
            }
            if(!$valid_size) {
                $sz = Sourcemap_Map_Static::$default_image_size;
            }
        } while(!$valid_size);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                header('Content-Type: image/png');
                $ckeyfmt = "static-map-%010d-%s-png";
                $cache_key = sprintf($ckeyfmt, $supplychain_id, $sz);
                $exists = Cache::instance()->get($cache_key);
                if($exists) {
                    header('X-Cache-Hit: true');
                    print $exists;
                } else {
                    // make blank image and enqueue job to generate
                    $maptic_url = Kohana::config('sourcemap.maptic_baseurl').
                        sprintf('static-map-sc%06d-%s.png', $supplychain_id, $sz);
                    $fetched = @file_get_contents($maptic_url);
                    if($fetched) {
                        print $fetched;
                        Cache::instance()->set($cache_key, $fetched, 3600);
                        exit;
                    } elseif($pimg = imagecreatefrompng(self::placeholder_image())) {
                        // pass
                        $pimgw = imagesx($pimg); $pimgh = imagesy($pimg);
                        $rpimgw = $szdim[2] - $szdim[0]; $rpimgh = $szdim[3] - $szdim[1];
                        $rpimg = imagecreatetruecolor($rpimgw, $rpimgh);
                        imagecopyresampled($rpimg, $pimg, 0, 0, 0, 0, $rpimgw, $rpimgh, $pimgw, $pimgh);
                        imagedestroy($pimg);
                        $pimg = $rpimg;
                    } else {
                        $pimg = imagecreatetruecolor($szdim[0], $szdim[1]);
                        imagecolorallocate($pimg, 0, 0, 255);
                    }
                    imagepng($pimg);
                    //header('Content-Type: text/plain');
                    Sourcemap::enqueue(Sourcemap_Job::STATICMAPGEN, array(
                        'supplychain' => $supplychain->kitchen_sink($supplychain->id),
                        'sizes' => Sourcemap_Map_Static::$image_sizes,
                        'thumbs' => Sourcemap_Map_Static::$image_thumbs
                    ));
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
