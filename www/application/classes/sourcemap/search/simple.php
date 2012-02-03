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

		// check to make sure stops has been counted for each map
		$stops_search = ORM::factory('supplychain_search');
		$stops_search->where('stops','is',null);
		$raw = $stops_search->find_all();
        $results = self::prep_rows($raw);
        if (count($results) > 0) {
			foreach($results as $row) {
				$stopcount_search = ORM::factory('supplychain_search',$row->search_id);
				$stopcount_search->stops = $row->stops_tot;
				$stopcount_search->save();
			}			
		}

        // by userid
        if(isset($this->parameters['user']) && (int)$this->parameters['user']) {
            $search->and_where('user_id', '=', $this->parameters['user']);
        }

        $search->reset(false);
        
        // The count_all method requires a separate call to the DB.  It's slow.
        // $ct = $search->count_all();
        // Let's just count the total number of results after they come in.

        // featured filter
        if(isset($this->parameters['featured']) && strtolower($this->parameters['featured']) == 'yes') {
            $search->and_where(DB::expr('featured'), 'and', DB::expr('TRUE'));
        }

        // user featured filter
        if(isset($this->parameters['user_featured']) && strtolower($this->parameters['user_featured']) == 'yes') {
            //$search->and_where(DB::expr('user_featured'), 'and', DB::expr('TRUE'));
            $search->and_where('user_featured','=','true');
        }

		// Don't display empty
        if(isset($this->parameters['display_empty']) && strtolower($this->parameters['display_empty']) == 'no') {
            $search->and_where('stops','>','0');
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

        //$tok = Profiler::start('bench','find-supplychains');
        $raw = $search->find_all();
        //Profiler::stop($tok);

        //$tok = Profiler::start('bench','prep-rows');
        $results = self::prep_rows($raw);
        //Profiler::stop($tok);

        $ct = count($results);
        
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
		$sca->search_id = $row->id;
        $sca->attributes = (object)$sc->attributes->find_all()->as_array("key", "value");
        if(isset($sca->attributes->passcode)){
            // If passcode exist, then return null
            return Null;
        }
        $sca->owner = (object)$sc->owner->as_array();
        $sca->owner->name = $sca->owner->username;
        $sca->comments_tot = ORM::factory('supplychain_comment')->where('supplychain_id', '=', $row->supplychain_id)->count_all();
        $sca->favorites_tot = ORM::factory('user_favorite')->where('supplychain_id', '=', $row->supplychain_id)->count_all();
		$sca->stops_tot = ORM::factory('stop')->where('supplychain_id', '=', $row->supplychain_id)->count_all();
        unset($sca->owner->password);
        unset($sca->owner->flags);
        unset($sca->owner->email); # !!!
        return $sca;
    }
}
