<?php
class Model_Supplychain extends ORM {
    
    public $_table_names_plural = false;

    protected $_updated_column = array(
        'column' => 'modified',
        'format' => true
    );

    protected $_created_column = array(
        'column' => 'created',
        'format' => true
    );

    public $_has_one = array(
        'owner' => array(
            'model' => 'supplychain_user'
        ),
        'owner_group' => array(
            'model' => 'supplychain_usergroup'
        )
    );

    public $_has_many = array(
        'stops' => array(
            'model' => 'stop',
            'foreign_key' => 'supplychain_id'
        ),
        'attributes' => array(
            'model' => 'supplychain_attribute',
            'foreign_key' => 'supplychain_id'
        )
    );

    public function save() {
        $this->modified = time();
        if(parent::save() && $this->pk()) {
            $rev = ORM::factory('supplychain_rev');
            $rev->supplychain_id = $this->pk();
            $rev->user_id = Auth::instance()->get_user() ? 
                Auth::instance()->get_user() : 0;
            $rev->data = json_encode($this->kitchen_sink());
            $rev->rev_hash = md5($rev->data.microtime());
            $rev->save();
        }
        return $this;
    }
    
    public function kitchen_sink() {
        // get everything you'd need for a supplychain.
        $supplychain = false;
        if($this->loaded() && $this->pk()) {
            $stops = array();
            $hops = array();
            foreach($this->stops->find_all()->as_array() as $i => $stop) {
                $attrs = $stop->attributes->find_all();
                $stop_arr = $stop->as_array();
                $stop_arr['attributes'] = (object)$attrs->as_array("key", "value");
                $stops[] = (object)$stop_arr;
                foreach($stop->hops->find_all()->as_array("id", true) as $j => $hop) {
                    $hops[] = (object)$hop;
                }
            }
            $attributes = $this->attributes
                ->find_all()->as_array('key', 'value');
            $supplychain = (object)$this->as_array();
            $supplychain->stops = $stops;
            $supplychain->hops = $hops;
            $supplychain->attributes = $attributes;
        }
        return $supplychain;
    }

    public function validate_raw_supplychain($data) {
        $valid = true;
        if(!isset($data->attributes)) {
            throw new Exception('Bad supplychain: attributes must be array.');
        }
        if(!isset($data->stops, $data->hops)) {
            die(print_r($data, true));
            throw new Exception('Bad supplychain: missing stops or hops.');
        }
        if(!is_array($data->stops) || !is_array($data->hops)) {
            throw new Exception('Bad supplychain: stops/hops must be arrays.');
        }
        $stop_ids = array();
        if($data->stops) {
            $stopmodel = ORM::factory('stop');
            foreach($data->stops as $stop) {
                $valid = $valid && $stopmodel->validate_raw_stop($stop, $stop_ids);
                $stop_ids[] = $stop->id;
            }
            if($data->hops) {
                $hopmodel = ORM::factory('hop');
                foreach($data->hops as $hop) {
                    $valid = $valid && $hopmodel->validate_raw_hop($hop, $stop_ids); 
                }
            }
        } elseif($data->hops) {
            throw new Exception('Bad supplychain: hops to nonexistent stops.');
        }
        return $valid;
    }
}
