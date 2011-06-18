<?php
class Sourcemap_Search_Simple extends Sourcemap_Search {
    public function fetch() {
        parent::fetch();
        if(!isset($this->parameters['q']))
            $this->parameters['q'] = '';

        $and_where = array();
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
                $clause_category = 'category in ('.join(',', $cat_ids).')';
                $and_where[] = $clause_category;
            } else {
                $clause_category = false;
            }
        }

        // featured filter
        if(isset($this->parameters['featured']) && strtolower($this->parameters['featured']) == 'yes') {
            $clause_featured = 'flags & '.Sourcemap::FEATURED.' > 0';
            $and_where[] = $clause_featured;
        }

        // recent filter
        if(isset($this->parameters['recent']) && strtolower($this->parameters['recent']) == 'yes') {
            $recent_featured = 'created > '.(time()-(2*7*24*60*60));
            $and_where[] = $recent_featured;
        }

        $selectsql = 'select distinct sc.id as supplychain_id';
        $fromsql = 'from stop_attribute sa '.
            'left join supplychain sc on (sa.supplychain_id=sc.id) '.
            'right join supplychain_attribute sca on (sca.supplychain_id=sc.id)';
        $clause_keyword = '(lower(sa.value) like \'%\'||:query||\'%\' '.
            'or lower(sca.value) like \'%\'||:query||\'%\')';
        $clause_public = 'other_perms & :readflag > 0';
        $limit_offset = 'limit :limit offset :offset';

        $sql = sprintf("%s where %s and %s", $fromsql, $clause_keyword, $clause_public);

        // add filter where clauses
        if($and_where) $sql .= ' and '.join(' and ', $and_where);

        $query = DB::query(Database::SELECT, $selectsql.' '.$sql.' '.$limit_offset);
        $query->param(':query', $this->parameters['q'])
            ->param(':readflag', Sourcemap::READ)
            ->param(':limit', $this->limit)
            ->param(':offset', $this->offset);
        $rows = $query->execute();

        $ctsql = 'select count(distinct sc.id) as hits '.$sql;
        $ctquery = DB::query(Database::SELECT, $ctsql);
        $ctquery->param(':query', $this->parameters['q'])
            ->param(':readflag', Sourcemap::READ);

        $results = array();
        foreach($rows as $i => $row) {
            $row = (object)$row;
            $sc = ORM::factory('supplychain', $row->supplychain_id);
            $sca = (object)$sc->as_array();
            $sca->attributes = (object)$sc->attributes->find_all()->as_array("key", "value");
            $sca->owner = (object)$sc->owner->find()->as_array();
            $sca->owner->name = $sca->owner->username;
            unset($sca->owner->password);
            unset($sca->owner->flags);
            unset($sca->owner->email); # !!!
            $results[] = $sca;
        }
        if($results) {
            $ctres = $ctquery->execute();
            if($ctres) {
                $ctres = $ctres->as_array();
                $ctres = $ctres[0];
                $this->results->hits_tot = $ctres['hits'];
            } else $this->results->hits_tot = 0;
        } else $this->results->hits_tot = 0;
        $this->results->results = $results;
        $this->results->limit = $this->limit;
        $this->results->offset = $this->offset;
        $this->results->hits_ret = count($results);
        return $this->results;
    }
}
