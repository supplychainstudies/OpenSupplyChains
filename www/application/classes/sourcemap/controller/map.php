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

class Sourcemap_Controller_Map extends Sourcemap_Controller_Layout {
    
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
        $sc = $supplychain->kitchen_sink($supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                $this->layout->supplychain_id = $supplychain_id;
               
                $supplychain_desc = "";
                
                // check description for shortcodes
                // only youtube ID is supported for now...
                if (isset($sc->attributes->description)) {
                    $supplychain_desc = $sc->attributes->description;
                    $regex = "/\\[youtube:([^]]+)]/";
                    if (preg_match($regex, $supplychain_desc, $regs)) {
                        $supplychain_youtube_id = $regs[1];
                        $supplychain_desc = str_replace($regs[0], '', $supplychain_desc);
                    }

                }

                // pass supplychain metadeta to template 
                $this->template->supplychain_id = $supplychain_id;
                $this->template->supplychain_date = date('F j, Y', $sc->created );
                $this->template->supplychain_name = isset($sc->attributes->title) ? $sc->attributes->title : (isset($sc->attributes->name) ? $sc->attributes->name : "");
                $this->template->supplychain_owner = isset($sc->owner->name) ? $sc->owner->name : "";
                $this->template->supplychain_banner_url = isset($sc->owner->banner_url) ? $sc->owner->banner_url : "";
                $this->template->supplychain_ownerid = isset($sc->owner->id) ? $sc->owner->id : "";
                $this->template->supplychain_avatar = isset($sc->owner->avatar) ? $sc->owner->avatar : "";
                $this->template->supplychain_desc = isset($supplychain_desc) ? $supplychain_desc : "" ;
                $this->template->supplychain_youtube_id = isset($supplychain_youtube_id) ? $supplychain_youtube_id : "" ;

    			$this->template->supplychain_taxonomy = isset($sc->taxonomy) ? $sc->taxonomy : array();
                
                $this->template->supplychain_weight = isset($sc->attributes->{'sm:ui:weight'}) ? "checked" : "";
                $this->template->supplychain_co2e = isset($sc->attributes->{"sm:ui:co2e"}) ? "checked" : "";
                $this->template->supplychain_water = isset($sc->attributes->{"sm:ui:water"}) ? "checked" : "";
                $this->template->supplychain_tileset = isset($sc->attributes->{"sm:ui:tileset"}) ? $sc->attributes->{"sm:ui:tileset"} : "";

    			$this->layout->page_title = $this->template->supplychain_name.' on Sourcemap';
    	        
                $this->template->can_edit = (bool)$supplychain->user_can($current_user_id, Sourcemap::WRITE);
                    
                
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
                    $arr['avatar'] = Gravatar::avatar($comment->user->email, 32);
                    $comment_data[] = (object)$arr;
                }
                $this->template->comments = $comment_data;
                $this->template->can_comment = (bool)$current_user_id;
                // qrcode url
    			$shortener = new Sourcemap_Bitly;
    			$shortlink = $shortener->shorten(URL::site('view/'.$supplychain->id, true));
                $qrcode_query = URL::query(array('q' => $shortlink, 'sz' => 3));
                $scaled_qrcode_query = URL::query(array('q' => $shortlink, 'sz' => 16));

    			$this->template->short_link = $shortlink;
                $this->template->qrcode_url = URL::site('services/qrencode', true).$qrcode_query;
                $this->template->scaled_qrcode_url = URL::site('services/qrencode', true).$scaled_qrcode_query;

            } else {
                Message::instance()->set('That map is private.');
                $this->request->redirect('browse');
            }
        } else {
            Message::instance()->set('That map could not be found.');
            $this->request->redirect('browse');
        }
    }

    public function action_create(){
        if(!Auth::instance()->get_user()) {
            Message::instance()->set('You must be signed in to create maps.');
            $this->request->redirect('auth');
        }

        $supplychain_id = '4';

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
                $qrcode_query = URL::query(array('q' => URL::site('view/'.$supplychain->id, true), 'sz' => 8));
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
                //header('Cache-Control: private,max-age=600');
                $ckeyfmt = "static-map-%010d-%s-png";
                //$cache_key = sprintf($ckeyfmt, $supplychain_id, $sz);
                $cache_key = Sourcemap_Map_Static::cache_key($supplychain_id, $sz);
                $exists = Cache::instance()->get($cache_key);
                if($exists) {
                    header('X-Cache-Hit: true');
                    print $exists;
                } else {
                    // make blank image and enqueue job to generate
                    $maptic_url = Kohana::config('sourcemap.maptic_baseurl').
                        sprintf('%s-static-map-sc%06d-%s.png', Sourcemap::$env, $supplychain_id, $sz);
                    $fetched = @file_get_contents($maptic_url);
                    if($fetched) {
                        print $fetched;
                        Cache::instance()->set($cache_key, $fetched, 300);
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
                        'baseurl' => Kohana_URL::site('/', true),
                        'environment' => Sourcemap::$env,
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
    	        $sc = $supplychain->kitchen_sink($supplychain_id);

    			$this->layout->page_title = (isset($sc->attributes->title) ? $sc->attributes->title : (isset($sc->attributes->name) ? $sc->attributes->name : "")).' on Sourcemap';

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
                    'downstream_sc' => null, 'tileset' => 'cloudmade',
                    'locate_user' => 'no', 'position' => '0|0|0',
					'served_as' => 'default'
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
                    ->rule('downstream_sc', 'numeric')
                    ->rule('position', 'not_empty')
                    ->rule('served_as', 'regex', array('/default|static|earth/i'));

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
                    $params['position'] = strtolower(trim($params['position']));
                    $params['served_as'] = strtolower(trim($params['served_as']));
                    /*
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
                    }*/
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
                return $this->request->redirect('view/'.$scid.'#comments');
            } else {
                $this->request->status = 400;
                Message::instance()->set('What good is a comment if it\'s empty?');
                return $this->request->redirect('view/'.$scid.'#comments');
            }
        } else {
            $this->request->status = 400;
            Message::instance()->set('You can\'t comment on nothing.');
            return $this->request->redirect('');
        }
    }
}
