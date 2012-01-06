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

 class Controller_Services_Supplychains extends Sourcemap_Controller_Service {
    public function action_get() {
        $id = $this->request->param('id', false);		
        if($id && !preg_match('/^\d+$/', $id)) {
            $alias = ORM::factory('supplychain_alias')
            ->where('site', '=', SOURCEMAP_SITE)
            ->where('alias', '=', $id)
            ->find_all()
            ->as_array('alias', 'supplychain_id');
            if(!isset($alias[$id])) {
                return $this->_bad_request('Invalid alias.');
            } else {
                $id = $alias[$id];
            }
        }
        $current_user = $this->get_current_user();
        if($current_user) { 
            $current_user = (object)$current_user->as_array();
            $user_id = $current_user->id;
        } else $user_id = null;
        if($id) {
            $exists = ORM::factory('supplychain', $id);
            if(!$exists) return $this->_not_found('Supplychain not found.');
            if(!$exists->user_can($user_id, Sourcemap::READ))
                return $this->_forbidden('You don\'t have permission '.
                    'to view that supplychain.');
            $cached = Cache::instance()->get('supplychain-'.$id);
            if($cached) {
                // check passcode
                if(isset($cached->attributes->passcode))
                {
                    $user_passcode = isset($_GET['passcode']) ? $_GET['passcode'] : "";
                    if($cached->attributes->passcode!=$user_passcode)
                        return $this->_bad_request('You entered the wrong passcode.');
                }

                $this->_cache_hit = true;
                $this->response = array(
                    'supplychain' => $cached
                );
            } else {                
                $fetched = false;
                try {
                    $fetched = ORM::factory('supplychain')->kitchen_sink($id);
                } catch(Exception $e) {
                    $fetched = false;
                }
                if(!$fetched) {
                    return $this->_not_found('Error retrieving supplychain.');
                }
                Cache::instance()->set('supplychain-'.$id, $fetched);
                // check passcode                
                if(isset($fetched->attributes->passcode))
                {
                    $user_passcode = $_GET['passcode'];
                    if($fetched->attributes->passcode!=$user_passcode)
                        return $this->_bad_request('Your entered the wrong passcode');
                }
                $this->response = array(
                    'supplychain' => $fetched
                );
            }
            $editable = $exists->user_can($user_id, Sourcemap::WRITE);
            $this->response['editable'] = $editable;
        } else {
            $switch_keys = array('featured');
            $switches = array();
            foreach($_GET as $k => $v) {
                $k = strtolower($k);
                if(in_array($k, $switch_keys)) {
                    if(strtolower($v) === 'yes')
                        $switches[] = $k;
                    break;
                }
            }
            $lparams = $this->_list_parameters();
            $cache_key = sprintf("supplychains-%d-%d", 
                $lparams->offset, $lparams->limit
            );
            sort($switches);
            $cache_key .= '-'.join('-', $switches);
            $supplychains = ORM::factory('supplychain');
            if($supplychain_list = Cache::instance()->get($cache_key)) {
                // pass
            } else {
                if(in_array('featured', $switches)) {
                    $supplychains->where(DB::expr('(flags & '.Sourcemap::FEATURED.')'), '>', 0);
                    $supplychains->and_where(DB::expr('(other_perms & '.Sourcemap::READ.')'), '>', 0);
                }
                $c = $supplychains->reset(false)->count_all();
                $supplychains->offset($lparams->offset)->limit($lparams->limit);
                $supplychain_list = array(
                    'supplychains' => $supplychains->find_all()
                        ->as_array(null, array('id', 'created')),
                    'total' => $c,
                    'limit' => $lparams->limit,
                    'offset' => $lparams->offset,
                    'switches' => $switches
                );
                Cache::instance()->set($cache_key, $supplychain_list);
            }
            $this->response = $supplychain_list;
        }
    }

    public function action_post() {
        $current_user = $this->get_current_user();
        if(!$current_user) {
            return $this->_forbidden('You must be signed in to create supplychains.');
        }
        $posted = $this->request->posted_data;
        try {
            if($this->_validate_raw_supplychain($posted)) {
                $raw_sc = $posted->supplychain;
                $raw_sc->user_id = $current_user->id;
                $new_scid = ORM::factory('supplychain')->save_raw_supplychain($raw_sc);
                $this->request->status = 201;
                $this->request->headers['Location'] = 'services/supplychains/'.$new_scid;
                $this->response = (object)array(
                    'created' => 'services/supplychains/'.$new_scid
                );
            }
        } catch(Exception $e) {
            return $this->_bad_request('Could not save supplychain: '.$e->getMessage()); 
        }
    }

    public function action_put() {
        $id = $this->request->param('id', false);
        if(!$id) {
            return $this->_bad_request('No id.');
        }
        if(!($supplychain = ORM::factory('supplychain', $id))) {
            return $this->_not_found('That supplychain does not exist.');
        }
        $current_user = $this->get_current_user();
        if(!$current_user) {
            return $this->_forbidden('You must be signed in to create or edit supplychains.');
        }
        if(!$supplychain->user_can($current_user->id, Sourcemap::WRITE)) {
            // TODO: use user_can method
            $user_groups = ORM::factory('user', $current_user->id)
                ->groups->find_all()->as_array('id', true);
            return $this->_forbidden(
                'You do not have permission to edit this supplychain.'
            );
        }
        $put = $this->request->put_data;
        try {
            if($this->_validate_raw_supplychain($put)) {
                $raw_sc = $put->supplychain;
                $retries = 3;
                $retry = true;
                while($retry && $retries--) {
                    $ecode = false;
                    try {
                        $supplychain->save_raw_supplychain($raw_sc, $id);
                        $retry = false;
                    } catch(Exception $e) {
                        $ecode = $supplychain->get_db()->get_pdo()->errorCode();
                        if($ecode === 40001) {
                            $retry = true;
                        }
                    }
                    if($ecode !== false && $ecode === 40001) {
                        throw new Exception('Dammit, Jim! The data! The data is not being saved!');
                    }
                }
                Sourcemap::enqueue(Sourcemap_Job::STATICMAPGEN, array(
                    'baseurl' => Kohana_URL::site('/', true),
                    'environment' => Sourcemap::$env,
                    'supplychain_id' => (int)$id,
                    'sizes' => Sourcemap_Map_Static::$image_sizes,
                    'thumbs' => Sourcemap_Map_Static::$image_thumbs
                ));
            } else throw new Exception('Invalid supplychain data: '.$e);
        } catch(Exception $e) {
            return $this->_bad_request('Could not save supplychain: '.$e->getMessage());
        }
        $this->request->status = 202;
        $this->response = (object)array(
            'success' => 'Supplychain updated.'
        );
    }

	public function action_delete() {        
        $id = $this->request->param('id', false);
        if(!$id) {
            return $this->_bad_request('No id.');
        }
        if(!($supplychain = ORM::factory('supplychain', $id))) {
            return $this->_not_found('That supplychain does not exist.');
        }
        $current_user = $this->get_current_user();
        if(!$current_user) {
            return $this->_forbidden('You must be signed in to create or edit supplychains.');
        }
        if(!$supplychain->user_can($current_user->id, Sourcemap::DELETE)) {
            // TODO: use user_can method
            $user_groups = ORM::factory('user', $current_user->id)
                ->groups->find_all()->as_array('id', true);
            return $this->_forbidden(
                'You do not have permission to delete this supplychain.'
            );
        }
        try {
            $supplychain->delete();			
        } catch(Exception $e) {
            return $this->_bad_request('Could not delete supplychain: '.$e->getMessage());
        }
        $this->request->status = 202;
        $this->response = (object)array(
            'success' => 'Supplychain deleted.'
        );
    }

    protected function _validate_raw_supplychain($data) {
        if(!isset($data->supplychain) || !is_object($data->supplychain)) {
            throw new Exception('Bad data: no supplychain.');
        }
        return ORM::factory('supplychain')
            ->validate_raw_supplychain($data->supplychain);
    }
 }
