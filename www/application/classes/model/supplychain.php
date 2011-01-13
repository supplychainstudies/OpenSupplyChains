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
    
    public function kitchen_sink($scid) {
        $scid = (int)$scid;
        if(($sc = ORM::factory('supplychain', $scid)) && $sc->loaded()) {
            $rows = $this->_db->query(Database::SELECT,
                sprintf("select s.id as stop_id, ST_AsText(s.geometry) as geometry, 
                        sa.key as attr_k, sa.value as attr_v
                    from stop as s left outer join stop_attribute as sa on
                        (s.id=sa.stop_id)
                    where s.supplychain_id = %d
                    order by sa.id desc",
                    $scid
                ), true
            )->as_array();
            $stops = array();
            foreach($rows as $i => $row) {
                if(!isset($stops[$row->stop_id])) {
                    $stops[$row->stop_id] = (object)array(
                        'id' => $row->stop_id,
                        'geometry' => $row->geometry,
                        'attributes' => (object)array()
                    );
                }
                if($row->attr_k) 
                    $stops[$row->stop_id]->attributes->{$row->attr_k} = $row->attr_v;
            }
            $hops = array();
            $sql = sprintf("select s.id as stop_id, h.id as hop_id, h.geometry as geometry,
                    ha.key as attr_k, ha.value as attr_v
                from stop as s 
                    left outer join hop as h on (s.id=h.from_stop_id)
                    left outer join hop_attribute as ha on (h.id=ha.hop_id)
                where s.supplychain_id=%d and h.id is not null", $scid
            );
            $rows = $this->_db->query(Database::SELECT, $sql, true);
            foreach($rows as $i => $row) {
                if(!isset($hops[$row->hop_id])) {
                    $hops[$row->hop_id] = (object)array(
                        'id' => $row->stop_id,
                        'geometry' => $row->geometry,
                        'attributes' => (object)array()
                    );
                }
                if($row->attr_k)
                    $hops[$row->stop_id]->attributes->{$row->attr_k} = $row->attr_v;
            }
            $sql = sprintf("select sca.key as attr_k, sca.value as attr_v
                from supplychain_attribute as sca
                where sca.supplychain_id=%d", $scid
            );
            $sc = (object)$sc->as_array();
            $sc->attributes = new stdClass();
            $rows = $this->_db->query(Database::SELECT, $sql, true);
            foreach($rows as $i => $row) {
                $sc->attributes->{$row->attr_k} = $row->attr_v;
            }
            $stops_arr = array();
            foreach($stops as $stop) $stops_arr[] = $stop;
            $hops_arr = array();
            foreach($hops as $hop) $hops_arr[] = $hop;
            $sc->stops = $stops_arr;
            $sc->hops = $hops_arr;
        } else throw new Exception('Supplychain not found.');
        return $sc;
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

    public function save_raw_supplychain($sc, $scid=null) {
        if(!$scid) {
            # todo: create here.
            $new_sc = ORM::factory('supplychain');
            $new_sc->user_id = isset($sc->user_id) ? $sc->user_id : null;
            $new_sc->save();
            $scid = $new_sc->id;
        } else {
            $sql = sprintf('delete from supplychain_attribute where supplychain_id = %d', $scid);
            $this->_db->query(Database::DELETE, $sql, true);
            $sql = sprintf('delete from stop where supplychain_id = %d', $scid);
            $this->_db->query(Database::DELETE, $sql, true);
        }
        # todo: concurrency? check last rev?
        $this->_db->query(null, 'BEGIN', true);
        try {
            $sql = sprintf('insert into stop (supplychain_id, geometry) values '.
                '(:supplychain_id, ST_SetSRID(ST_GeometryFromText(:geometry), %d))',
                Sourcemap::PROJ
            );
            $query = DB::query(Database::INSERT, $sql, true)->param(':supplychain_id', $scid);
            $last_insert_query = DB::query(Database::SELECT, 'select currval(\'stop_id_seq\') as stop_seq');
            $stattr_sql = 'insert into stop_attribute (stop_id, "key", "value") values (:stop_id, :key, :value)';
            $stattr_insert_query = DB::query(Database::INSERT, $stattr_sql);
            $stop_map = array();
            foreach($sc->stops as $sti => $raw_stop) {
                list($nothing, $affected) = $query->param(':geometry', $raw_stop->geometry)->execute();
                if($affected && $last_insert = $last_insert_query->execute()) {
                    $stop_map[$raw_stop->id] = $last_insert[0]['stop_seq'];
                } else throw new Exception('Could not insert stop.');
                foreach($raw_stop->attributes as $k => $v) {
                    list($nothing, $affected) = $stattr_insert_query->param(':stop_id', $stop_map[$raw_stop->id])
                        ->param(':key', $k)->param(':value', $v)->execute();
                    if(!$affected) throw new Exception('Could not insert stop attribute: "'.$k.'".');
                }
            }
            $hop_insert_query = DB::query(Database::INSERT, 
                'insert into hop (to_stop_id,from_stop_id,geometry) values '.
                '(:to_stop_id, :from_stop_id, :geometry)'
            );
            $last_insert_query = DB::query(Database::SELECT, 'select currval(\'hop_id_seq\') as stop_seq');
            $hattr_sql = 'insert into stop_attribute (hop_id, "key", "value") values (:hop_id, :key, :value)';
            $hattr_insert_query = DB::query(Database::INSERT, $hattr_sql);
            foreach($sc->hops as $hi => $raw_hop) {
                list($nothing, $affected) = $hop_insert_query
                    ->param(':to_stop_id', $stop_map[$raw_hop->to_stop_id])
                    ->param(':from_stop_id', $stop_map[$raw_hop->from_stop_id])
                    ->param(':geometry', $raw_hop->geometry)
                    ->execute();
                if($affected && $last_insert = $last_insert_query->execute()) {
                    $new_hop_id = (int)$last_insert[0]['stop_seq'];
                } else throw new Exception('Could not insert hop.');
                foreach($raw_hop->attributes as $k => $v) {
                    list($nothing, $affected) = $hattr_insert_query->param(':hop_id', $new_hop_id)
                        ->param(':key', $k)->param(':value', $v)->execute();
                    if(!$affected) throw new Exception('Could not insert hop attribute: "'.$k.'".');
                }
            }
            if(isset($sc->usergroup_perms)) {
                $sc->usergroup_perms = (int)$sc->usergroup_perms;
                $sql = sprintf(
                    'update supplychain set usergroup_perms = %d where id = %d', 
                    $sc->usergroup_perms, $scid
                );
                $this->_db->query(Database::UPDATE, $sql);
            }
            if(isset($sc->other_perms)) {
                $sc->other_perms = (int)$sc->other_perms;
                $sql = sprintf(
                    'update supplychain set other_perms = %d where id = %d', 
                    $sc->other_perms, $scid
                );
                $this->_db->query(Database::UPDATE, $sql);
            }
        } catch(Exception $e) {
            $this->_db->query(null, 'ROLLBACK', true);
            throw new Exception('Could not save raw suppychain with id "'.$scid.'"('.$e->getMessage().')');
        }
        $this->_db->query(null, 'COMMIT', true);
        return $scid;
    }

    public function user_can($user_id, $mode) {
        $can = false;
        if($this->loaded()) {
            $owner_id = (int)$this->user_id;
            $user_id = (int)$user_id;
            // owner?
            if($owner_id === $user_id) {
                $can = true;
            }
            // group?
            $user = ORM::factory('user', $user_id);
            if(!$can && $this->usergroup_id) {
                $owner_group_id = (int)$this->usergroup_id;
                if($owner_group_id && $this->usergroup_perms & $mode) {
                    if($user->has('groups', ORM::factory('usergroup', $this->group_id))) {
                        $can = true;
                    }
                }
            }
            // other?
            if(!$can && $this->other_perms & $mode) {
                $can = true;
            }
            // admin?
            if(!$can && $user) {
                $admin = ORM::factory('role')
                    ->where('name', '=', 'adminstrator')->find();
                if($user->has('roles', $admin)) {
                    $can = true;
                }
            }
        }
        return $can;
    }
}
