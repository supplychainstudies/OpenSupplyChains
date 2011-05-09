<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
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
        if($id) {
            $cached = Cache::instance()->get('supplychain-'.$id);
            if($cached) {
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
                    return $this->_not_found('Supplychain not found.');
                }
                Cache::instance()->set('supplychain-'.$id, $fetched);
                $this->response = array(
                    'supplychain' => $fetched
                );
            }
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
            if($supplychains = Cache::instance()->get($cache_key)) {
                // pass
            } else {
                $supplychains = ORM::factory('supplychain')
                    ->offset($lparams->offset)->limit($lparams->limit);
                if(in_array('featured', $switches)) {
                    $supplychains->where(DB::expr('(flags & '.Sourcemap::FEATURED.')'), '>', 0);
                }
                $supplychains = $supplychains->find_all()
                    ->as_array('id', array('id', 'created'));
                Cache::instance()->set($cache_key, $supplychains);
            }
            $this->response = array(
                'supplychains' => $supplychains,
                'parameters' => $lparams,
                'switches' => $switches,
                'total' => ORM::factory('supplychain')->count_all()
            );
        }
    }

    public function action_post() {
        $current_user = Auth::instance()->get_user();
        if(!$current_user) {
            return $this->_forbidden('You must be logged in to create supplychains.');
        }
        $posted = $this->request->posted_data;
        try {
            if($this->_validate_raw_supplychain($posted)) {
                $raw_sc = $posted->supplychain;
                $raw_sc->user_id = $current_user;
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
        error_log(__METHOD__);
        $id = $this->request->param('id', false);
        if(!$id) {
            return $this->_bad_request('No id.');
        }
        if(!($supplychain = ORM::factory('supplychain', $id))) {
            return $this->_not_found('That supplychain does not exist.');
        }
        $current_user = Auth::instance()->logged_in() ? Auth::instance()->get_user() : false;
        if(!$current_user) {
            return $this->_forbidden('You must be logged in to create supplychains.');
        }
        if((int)$current_user->id !== (int)$supplychain->user_id) {
            // todo: use user_can method
            $user_groups = ORM::factory('user', $current_user)
                ->groups->find_all()->as_array('id', true);
            return $this->_forbidden(
                'You do not have permission to edit this supplychain.'
            );
        }
        $put = $this->request->put_data;
        try {
            if($this->_validate_raw_supplychain($put)) {
                $raw_sc = $put->supplychain;
                $supplychain->save_raw_supplychain($raw_sc, $id);
            }
        } catch(Exception $e) {
            return $this->_bad_request('Could not save supplychain: '.$e->getMessage());
        }
        $this->request->status = 202;
        $this->response = (object)array(
            'success' => 'Supplychain updated.'
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
