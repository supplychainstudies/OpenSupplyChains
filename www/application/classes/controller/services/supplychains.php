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
            $supplychain = ORM::factory('supplychain', $id);
            if(!$supplychain->loaded()) {
                return $this->_not_found('Supplychain not found.');
            }
            $this->response = array(
                'supplychain' => $supplychain->kitchen_sink()
            );
        } else {
            $params = $this->_list_parameters();
            $supplychains = ORM::factory('supplychain')
                ->offset($params->offset)->limit($params->limit)
                ->find_all()->as_array('id', array('id', 'created'));
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
                /*foreach($supplychain->stops as $i => $raw_stop) {
                    $new_stop = ORM::factory('stop');
                    $new_stop->
                }*/
                $this->request->status = 201;
                $this->request->headers['Location'] = 'services/supplychains/'.$new_sc->id;
                $this->response = (object)array(
                    'created' => 'services/supplychains/'.$new_sc->id
                );
            }
        } catch(Exception $e) {
            return $this->_bad_request('Unusable supplychain: '.$e->getMessage()); 
        }
    }

    protected function _validate_raw_supplychain($data) {
        if(!isset($data->supplychain) || !is_object($data->supplychain))
            throw new Exception('Bad data: no supplychain.');
        return ORM::factory('supplychain')
            ->validate_raw_supplychain($data->supplychain);
    }
 }
