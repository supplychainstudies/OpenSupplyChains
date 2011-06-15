<?php
class Controller_Map extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
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
                $this->layout->supplychain_id = $supplychain_id;
                $this->template->supplychain_id = $supplychain_id;
                $this->layout->scripts = array('map-view');
                $this->layout->styles = array(
                    'sites/default/assets/styles/reset.css', 
                    'assets/styles/base.less',
                    'assets/styles/general.less'
                );
                // comments
                $c = $supplychain->comments->find_all();
                $comment_data = array();
                foreach($c as $i => $comment) {
                    $arr = $comment->as_array();
                    $arr['username'] = $comment->user->username;
                    $arr['avatar'] = Gravatar::avatar($comment->user->email);
                    $comment_data[] = (object)$arr;
                }
                $this->template->comments = $comment_data;
                $this->template->can_comment = (bool)$current_user_id;
                // qrcode url
                $qrcode_query = URL::query(array('q' => URL::site('map/view/'.$supplychain->id, true), 'sz' => 8));
                $this->template->qrcode_url = URL::site('services/qrencode', true).$qrcode_query;
            } else {
                Message::instance()->set('That map is private.');
                $this->request->redirect('browse');
            }
        } else {
            Message::instance()->set('That map could not be found.');
            $this->request->redirect('browse');
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
                        if(count($szdim) == 2) {
                            $rpimgw = $szdim[0]; $rpimgh = $szdim[1];
                        } elseif(count($szdim) == 4) {
                            $rpimgw = $szdim[2] - $szdim[0]; $rpimgh = $szdim[3] - $szdim[1];
                        }
                        $rpimg = imagecreatetruecolor($rpimgw, $rpimgh);
                        imagecopyresampled($rpimg, $pimg, 0, 0, 0, 0, $rpimgw, $rpimgh, $pimgw, $pimgh);
                        imagedestroy($pimg);
                        $pimg = $rpimg;
                    } else {
                        $pimg = imagecreatetruecolor($szdim[0], $szdim[1]);
                        imagecolorallocate($pimg, 0, 0, 255);
                    }
                    imagepng($pimg);
                    Sourcemap::enqueue(Sourcemap_Job::STATICMAPGEN, array(
                        'supplychain_id' => $supplychain->id,
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
                    'assets/styles/base.less',
                    'assets/styles/embed.less',
                    'assets/styles/general.less'
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

    public function action_comment($scid) {
        if(!($current_user = Auth::instance()->get_user()) || !$current_user->loaded()) {
            $this->request->status = 403;
            Message::instance()->set('You must be logged in to comment.');
            return $this->request->redirect('');
        }
        $sc = ORM::factory('supplychain', $scid);
        if($sc->loaded()) {
            $p = Validate::factory($_POST);
            $p->rule('body', 'not_empty');
            if($p->check()) {
                $new_comment = ORM::factory('supplychain_comment');
                $new_comment->body = $p['body'];
                $new_comment->user_id = $current_user->id;
                $new_comment->supplychain_id = $scid;
                $new_comment->timestamp = time();
                try {
                    $new_comment->save();
                    Message::instance()->set('Your comment was saved.', Message::SUCCESS);
                } catch(Exception $e) {
                    $this->request->status = 500;
                    Message::instance()->set('There was a problem saving your comment.');
                }
                return $this->request->redirect('map/view/'.$scid.'#comments');
            } else {
                $this->request->status = 400;
                Message::instance()->set('What good is a comment if it\'s empty?');
                return $this->request->redirect('map/view/'.$scid.'#comments');
            }
        } else {
            $this->request->status = 400;
            Message::instance()->set('You can\'t comment on nothing.');
            return $this->request->redirect('');
        }
    }
}
