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

    public $_belongs_to = array(
        'owner' => array(
            'model' => 'user', 'foreign_key' => 'user_id' 
        ),
        'owner_group' => array(
            'model' => 'usergroup', 'foreign_key' => 'usergroup_id'
        ),
        'taxonomy' => array(
            'model' => 'category',
            'far_key' => 'id',
            'foreign_key' => 'category'
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
        ),
        'hops' => array(
            'model' => 'hop',
            'foreign_key' => 'supplychain_id'
            ),
        'alias' => array(
            'model' => 'supplychain_alias',
            'foreign_key' => 'supplychain_id'
        ),
        'favorited_by' => array(
            'model' => 'user',
            'through' => 'user_favorite'
        ),
        'comments' => array(
            'model' => 'supplychain_comment',
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
            $rev->data = json_encode($this->kitchen_sink($this->pk()));
            $rev->rev_hash = md5($rev->data.microtime());
            $rev->save();
            $scid = $this->id;
            Cache::instance()->delete('supplychain-'.$scid);
        }
        return $this;
    }

    public function kitchen_sink($scid) {
        $scid = (int)$scid;
        if(($sc = ORM::factory('supplychain', $scid)) && $sc->loaded()) {
            $rows = $this->_db->query(Database::SELECT,
                sprintf("select s.id as stop_id, ST_AsText(s.geometry) as geometry, 
                        sa.key as attr_k, sa.value as attr_v, s.local_stop_id as local_stop_id
                    from stop as s left outer join stop_attribute as sa on
                        (s.supplychain_id=sa.supplychain_id and s.local_stop_id=sa.local_stop_id)
                    where s.supplychain_id = %d
                    order by sa.id desc",
                    $scid
                ), true
            )->as_array();
            $stops = array();
            foreach($rows as $i => $row) {
                if(!isset($stops[$row->local_stop_id])) {
                    $stops[$row->local_stop_id] = (object)array(
                        'local_stop_id' => $row->local_stop_id,
                        'id' => $row->local_stop_id,
                        'geometry' => $row->geometry,
                        'attributes' => (object)array()
                    );
                }
                if($row->attr_k) 
                    $stops[$row->local_stop_id]->attributes->{$row->attr_k} = $row->attr_v;
            }
            $hops = array();
            $sql = sprintf("select h.id as hop_id, h.from_stop_id, h.to_stop_id,
                ST_AsText(h.geometry) as geometry, ha.key as attr_k, ha.value as attr_v
                from hop as h 
                    left outer join hop_attribute as ha on (
                        h.supplychain_id=ha.supplychain_id and
                        h.from_stop_id=ha.from_stop_id and
                        h.to_stop_id=ha.to_stop_id
                    )
                where h.supplychain_id = %d order by h.id asc", $scid);
            $rows = $this->_db->query(Database::SELECT, $sql, true);
            foreach($rows as $i => $row) {
                $hkey = sprintf("%d-%d", $row->from_stop_id, $row->to_stop_id);
                if(!isset($hops[$hkey])) {
                    $hops[$hkey] = (object)array(
                        'from_stop_id' => $row->from_stop_id,
                        'to_stop_id' => $row->to_stop_id,
                        'geometry' => $row->geometry,
                        'attributes' => (object)array()
                    );
                }
                if($row->attr_k)
                    $hops[$hkey]->attributes->{$row->attr_k} = $row->attr_v;
            }
            $sql = sprintf("select sca.key as attr_k, sca.value as attr_v
                from supplychain_attribute as sca
                where sca.supplychain_id=%d", $scid
            );
            $owner = $sc->owner;
            $cat = $sc->taxonomy;
            $sc = (object)$sc->as_array();
            $sc->attributes = new stdClass();
            $rows = $this->_db->query(Database::SELECT, $sql, true);
            foreach($rows as $i => $row) {
                $sc->attributes->{$row->attr_k} = $row->attr_v;
            }
            $sc->stops = array_values($stops);
            $sc->hops = array_values($hops);
            $sc->owner = (object)array(
                'id' => $owner->id, 'name' => $owner->username,
                //'avatar' => Gravatar::avatar($owner->email),
                'avatar' => "services/uploads?bucket=accountpics&filename=".$owner->username,
                'banner_url' => $owner->banner_url,
                'display_name' => $owner->display_name
            );
            $sc->user_id = $owner->id;
            $sc->taxonomy = 
                ($cat && $cat->loaded()) ? Sourcemap_Taxonomy::load_ancestors($cat->id) : null;
        } else throw new Exception('Supplychain not found.');
        return $sc;
    }

    public function validate_raw_supplychain($data) {
        $valid = true;
        if(!isset($data->attributes)) {
            throw new Exception('Bad supplychain: attributes must be array.');
        }
        if(!isset($data->stops, $data->hops)) {
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
                $stop_ids[] = $stop->local_stop_id;
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
        $this->_db->query(null, 'BEGIN TRANSACTION ISOLATION LEVEL SERIALIZABLE', true);
        if(!$scid) {
            # TODO: create here.
            $new_sc = ORM::factory('supplychain');
            $new_sc->user_id = isset($sc->user_id) ? $sc->user_id : null;
            $new_sc->save();
            $scid = $new_sc->id;
        } else {
            $sc->user_id = ORM::factory('supplychain', $scid)->user_id;
            $sql = sprintf('delete from supplychain_attribute where supplychain_id = %d', $scid);
            $this->_db->query(Database::DELETE, $sql, true);
            $sql = sprintf('delete from stop where supplychain_id = %d', $scid);
            $this->_db->query(Database::DELETE, $sql, true);
            $sql = sprintf('delete from hop where supplychain_id = %d', $scid);
            $this->_db->query(Database::DELETE, $sql, true);
        }
        # TODO: concurrency? check last rev?
        try {
            $scattr_sql = 'insert into supplychain_attribute (supplychain_id, "key", "value") '.
                'values (:supplychain_id, :key, :value)';
            $scattr_insert_query = DB::query(Database::INSERT, $scattr_sql);
            foreach($sc->attributes as $k => $v) {
                // if passcode is set to none, don't set attributes
                if($k==="passcode")
                    if($v==="")
                        continue;
                list($nothing, $affected) = $scattr_insert_query->param(':supplychain_id', $scid)
                    ->param(':key', $k)->param(':value', (string)$v)->execute();
                if(!$affected) throw new Exception('Could not insert supplychain attribute: "'.$k.'".');
            }
            $sql = sprintf('insert into stop (supplychain_id, local_stop_id, geometry) values '.
                '(:supplychain_id, :local_stop_id, ST_SetSRID(ST_GeometryFromText(:geometry), %d))',
                Sourcemap::PROJ
            );
            $query = DB::query(Database::INSERT, $sql, true)->param(':supplychain_id', $scid);
            $last_insert_query = DB::query(Database::SELECT, 'select currval(\'stop_id_seq\') as stop_seq');
            $stattr_sql = 'insert into stop_attribute (supplychain_id, local_stop_id, "key", "value") '.
                'values (:supplychain_id, :local_stop_id, :key, :value)';
            $stattr_insert_query = DB::query(Database::INSERT, $stattr_sql);
            foreach($sc->stops as $sti => $raw_stop) {
                list($nothing, $affected) = $query->param(':local_stop_id', $raw_stop->local_stop_id)->param(':geometry', $raw_stop->geometry)->execute();
                if(!$affected)
                    throw new Exception('Could not insert stop.');
                foreach($raw_stop->attributes as $k => $v) {
                    list($nothing, $affected) = $stattr_insert_query->param(':supplychain_id', $scid)->param(':local_stop_id', $raw_stop->local_stop_id)
                        ->param(':key', $k)->param(':value', $v)->execute();
                    if(!$affected) throw new Exception('Could not insert stop attribute: "'.$k.'".');
                }
            }
            $hop_insert_query = DB::query(Database::INSERT, 
                'insert into hop (supplychain_id, to_stop_id, from_stop_id,geometry) values '.
                '(:supplychain_id, :to_stop_id, :from_stop_id, ST_SetSRID(ST_GeometryFromText(:geometry), '.Sourcemap::PROJ.'))'
            );
            $last_insert_query = DB::query(Database::SELECT, 'select currval(\'hop_id_seq\') as stop_seq');
            $hattr_sql = 'insert into hop_attribute (supplychain_id, from_stop_id, to_stop_id, "key", "value")'.
                ' values (:supplychain_id, :from_stop_id, :to_stop_id, :key, :value)';
            $hattr_insert_query = DB::query(Database::INSERT, $hattr_sql);
            foreach($sc->hops as $hi => $raw_hop) {
                list($nothing, $affected) = $hop_insert_query
                    ->param(':supplychain_id', $scid)
                    ->param(':to_stop_id', $raw_hop->to_stop_id)
                    ->param(':from_stop_id', $raw_hop->from_stop_id)
                    ->param(':geometry', $raw_hop->geometry)
                    ->execute();
                if(!$affected) 
                    throw new Exception('Could not insert hop.');
                foreach($raw_hop->attributes as $k => $v) {
                    list($nothing, $affected) = $hattr_insert_query
                        ->param(':supplychain_id', $scid)
                        ->param(':from_stop_id', $raw_hop->from_stop_id)
                        ->param(':to_stop_id', $raw_hop->to_stop_id)
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
                $this->_db->query(Database::UPDATE, $sql, true);
            }
            if(isset($sc->other_perms)) {
                $sc->other_perms = (int)$sc->other_perms;
                $sql = sprintf(
                    'update supplychain set other_perms = %d where id = %d', 
                    $sc->other_perms, $scid
                );
                $this->_db->query(Database::UPDATE, $sql, true);
            }
            // channel user featured
            if(isset($sc->user_featured)) {
                if($sc->user_featured)
                    $user_featured = "TRUE";
                else
                    $user_featured = "FALSE";
                $sql = sprintf(
                    'update supplychain set user_featured = %s where id = %d', 
                    $user_featured, $scid
                );
                $this->_db->query(Database::UPDATE, $sql, true);
            }

            if(isset($sc->category)) {
                if(ORM::factory('category', $sc->category)->loaded()) {
                    $sql = sprintf(
                        'update supplychain set category = %d where id = %d',
                        $sc->category, $scid
                    );
                    $this->_db->query(Database::UPDATE, $sql, true);
                } else {
                    throw new Exception('Invalid category '.(int)$sc->category);
                }
            }
        } catch(Exception $e) {
            $this->_db->query(null, 'ROLLBACK', true);
            throw new Exception('Could not save raw supplychain with id "'.$scid.'"('.$e->getMessage().')');
        }
        $this->_db->query(null, 'COMMIT', true);
        $evt = isset($new_sc) ? Sourcemap_User_Event::CREATEDSC : Sourcemap_User_Event::UPDATEDSC;
        try {
            Sourcemap_User_Event::factory($evt, $sc->user_id, $scid)->trigger();
        } catch(Exception $e) {
            // pass
            //die($e);
        }
        Cache::instance()->delete('supplychain-'.$scid);
        if(Sourcemap_Search_Index::should_index($scid)) {
            Sourcemap_Search_Index::update($scid);
        } else {
            Sourcemap_Search_Index::delete($scid);
        }
        $szs = Sourcemap_Map_Static::$image_sizes;
        foreach($szs as $snm => $sz) {
            $ckey = Sourcemap_Map_Static::cache_key($scid, $snm);
            Cache::instance()->delete($ckey);
        }
        $sc = ORM::factory('supplychain', $scid);
        $sc->modified = time();
        $sc->save();
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
                    ->where('name', '=', 'admin')->find();
                if($user->has('roles', $admin)) {
                    $can = true;
                }
            }
        }
        return $can;
    }
}
