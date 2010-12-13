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
        if($id) {
            if($cached = Cache::instance()->get('supplychain-'.$id)) {
                $this->response = array(
                    'supplychain' => unserialize($cached)
                );
            } else {
                $supplychain = ORM::factory('supplychain', $id);
                if(!$supplychain->loaded()) {
                    return $this->_not_found('Supplychain not found.');
                }
                $fetched = $supplychain->kitchen_sink();
                Cache::instance()->set('supplychain-'.$id, serialize($fetched));
                $this->response = array(
                    'supplychain' => $fetched
                );
            }
        } else {
            $params = $this->_list_parameters();
            $cache_key = sprintf("supplychains-%d-%d", 
                $params->offset, $params->limit
            );
            if($supplychains = Cache::instance()->get($cache_key)) {
                $supplychains = unserialize($supplychains);    
            } else {
                $supplychains = ORM::factory('supplychain')
                    ->offset($params->offset)->limit($params->limit)
                    ->find_all()->as_array('id', array('id', 'created'));
                Cache::instance()->set($cache_key, serialize($supplychains));
            }
            $this->response = array(
                'supplychains' => $supplychains,
                'parameters' => $params, 
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
                $new_sc = ORM::factory('supplychain');
                $new_sc->user_id = $current_user;
                $new_sc->save();
                foreach($raw_sc->attributes as $k => $v) {
                    $new_sc_attr = ORM::factory('supplychain_attribute');
                    $new_sc_attr->key = $k;
                    $new_sc_attr->value = (string)$v;
                    $new_sc_attr->supplychain_id = $new_sc->id;
                    $new_sc_attr->save();
                }
                $local_stop_ids = array();
                foreach($raw_sc->stops as $i => $raw_stop) {
                    $new_stop = ORM::factory('stop');
                    $new_stop->geometry = $raw_stop->geometry;
                    $new_stop->supplychain_id = $new_sc->id;
                    $new_stop->save();
                    $local_stop_ids[(int)$raw_stop->id] = (int)$new_stop->id;
                    foreach($raw_stop->attributes as $k => $v) {
                        $new_stop_attr = ORM::factory('stop_attribute');
                        $new_stop_attr->stop_id = $new_stop->id;
                        $new_stop_attr->{'key'} = $k;
                        $new_stop_attr->value = $v;
                        $new_stop_attr->save();
                    }
                }
                foreach($raw_sc->hops as $i => $raw_hop) {
                    $new_hop = ORM::factory('hop');
                    $new_hop->geometry = $raw_hop->geometry;
                    $new_hop->from_stop_id = $local_stop_ids[(int)$raw_hop->from_stop_id];
                    $new_hop->to_stop_id = $local_stop_ids[(int)$raw_hop->to_stop_id];
                    $new_hop->save();
                    foreach($raw_hop->attributes as $k => $v) {
                        $new_hop_attr = ORM::factory('hop_attribute');
                        $new_hop_attr->hop_id = $new_hop->id;
                        $new_hop_attr->{'key'} = $k;
                        $new_hop_attr->value = $v;
                        $new_hop_attr->save();
                    }
                    
                }

                $this->request->status = 201;
                $this->request->headers['Location'] = 'services/supplychains/'.$new_sc->id;
                $this->response = (object)array(
                    'created' => 'services/supplychains/'.$new_sc->id
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
        $current_user = Auth::instance()->get_user();
        if(!$current_user) {
            return $this->_forbidden('You must be logged in to create supplychains.');
        }
        if((int)$current_user->id !== (int)$supplychain->user_id) {
            $user_groups = ORM::factory('user', $current_user)
                ->groups->find_all()->as_array('id', true);
            return $this->_forbidden(
                'You do not have permission to edit this supplychain.'
            );
        }
        $put = $this->request->put_data;
        try {
            if($this->_validate_raw_supplychain($put));
        } catch(Exception $e) {
            return $this->_bad_request('Could not save supplychain: '.$e->getMessage());
        }
    }

    protected function _validate_raw_supplychain($data) {
        if(!isset($data->supplychain) || !is_object($data->supplychain)) {
            throw new Exception('Bad data: no supplychain.');
        }
        return ORM::factory('supplychain')
            ->validate_raw_supplychain($data->supplychain);
    }
 }
