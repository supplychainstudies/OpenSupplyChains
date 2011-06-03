<?php
class Sourcemap_Search_Simple extends Sourcemap_Search {
    public function fetch() {
        parent::fetch();
        //$scm = ORM::factory('supplychain');
        /*$rows = $scm->limit($this->limit)
            ->offset($this->offset)
            ->where(DB::expr("other_perms & ".(int)Sourcemap::READ), '>', 0)
            ->find_all();
        */
        if(!isset($this->parameters['q']))
            $this->parameters['q'] = '';
        $sql = 'select distinct sc.id as supplychain_id from stop_attribute sa '.
            'left join supplychain sc on (sa.supplychain_id=sc.id) '.
            'where lower(sa.value) like \'%\'||:query||\'%\' limit :limit offset :offset';
        $query = DB::query(Database::SELECT, $sql);
        $query->param(':query', $this->parameters['q'])
            ->param(':limit', $this->limit)
            ->param(':offset', $this->offset);
        $rows = $query->execute();

        $ctsql = 'select distinct count(sc.id) as hits from stop_attribute sa '.
            'left join supplychain sc on (sa.supplychain_id=sc.id) '.
            'where lower(sa.value) like \'%\'||:query||\'%\'';
        $ctquery = DB::query(Database::SELECT, $ctsql);
        $ctquery->param(':query', $this->parameters['q']);

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
