<?php
class Sourcemap_Search_Simple extends Sourcemap_Search {
    public function fetch() {
        parent::fetch();
        if(!isset($this->parameters['q']))
            $this->parameters['q'] = '';

        $search = ORM::factory('supplychain_search');

        // category filter
        $clause_category = false;
        if(isset($this->parameters['c'])) {
            $cat = $this->parameters['c'];
            $csql = 'select id from category where name ilike \'%\'||:catq||\'%\'';
            $query = DB::query(Database::SELECT, $csql);
            $query->param(':catq', $cat);
            $rows = $query->execute();
            $cat_ids = array();
            foreach($rows as $i => $row) {
                $cat_ids[] = $row['id'];
                $children = Sourcemap_Taxonomy::load_children($row['id']);
                foreach($children as $j => $child) {
                    if(!in_array($child->id, $cat_ids)) $cat_ids[] = $child->id;
                }
            }
            if($cat_ids) {
                $search->and_where('category', 'in', $cat_ids);
            } else {
                // pass 
            }
        }

        if(isset($this->parameters['q']) && $this->parameters['q']) {
            $qts = preg_split('/\s+/', $this->parameters['q'], null, PREG_SPLIT_NO_EMPTY);
            $q = array();
            foreach($qts as $i => $qt) 
                $q[] = $qt;
            if($q) {
                $search->and_where(
                    DB::expr('to_tsvector(body)'), '@@', 
                    DB::expr('plainto_tsquery(\'english\','.
                        Database::instance()->quote(join(' ', $q)).
                    ')')
                );
            }
        }
        
        // by userid
        if(isset($this->parameters['user']) && (int)$this->parameters['user']) {
            $search->and_where('user_id', '=', $this->parameters['user']);
        }

        $search->reset(false);
        $ct = $search->count_all();
        

        // featured filter
        if(isset($this->parameters['featured']) && strtolower($this->parameters['featured']) == 'yes') {
            $search->and_where(DB::expr('featured'), 'and', DB::expr('TRUE'));
        }

        // recent filter
        if(isset($this->parameters['recent']) && strtolower($this->parameters['recent']) == 'yes') {
            $search->order_by('created', 'desc');
        }

        // most commented
        if(isset($this->parameters['comments']) && strtolower($this->parameters['comments']) == 'yes') {
            $search->order_by('comments', 'desc');
        }
        
        // most favorited
        if(isset($this->parameters['favorited']) && strtolower($this->parameters['favorited']) == 'yes') {
            $search->order_by('favorited', 'desc');
        }

        $search->limit($this->limit);
        $search->offset($this->offset);

        $raw = $search->find_all();
        $results = self::prep_rows($raw);

        $this->results->hits_tot = $ct;
        $this->results->results = $results;
        $this->results->limit = $this->limit;
        $this->results->offset = $this->offset;
        $this->results->hits_ret = count($results);
        $this->results->parameters = $this->parameters;
        return $this->results;
    }

    public static function prep_rows($rows) {
        $prepped = array();
        foreach($rows as $i => $row) {
            $prepped_row = self::prep_row($row);
            if($prepped_row)
                $prepped[] = $prepped_row;
        }
        return $prepped;
    }

    public static function prep_row($row) {
        $row = (object)$row;
        $sc = ORM::factory('supplychain', $row->supplychain_id);
        $sca = (object)$sc->as_array();
        $sca->attributes = (object)$sc->attributes->find_all()->as_array("key", "value");
        $sca->owner = (object)$sc->owner->as_array();
        $sca->owner->name = $sca->owner->username;
        unset($sca->owner->password);
        unset($sca->owner->flags);
        unset($sca->owner->email); # !!!
        return $sca;
    }
}
