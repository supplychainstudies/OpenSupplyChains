<?php
class Sourcemap_Search_Simple extends Sourcemap_Search {
    public function fetch() {
        parent::fetch();
        $scm = ORM::factory('supplychain');
        $rows = $scm->limit($this->limit)
            ->offset($this->offset)
            ->where(DB::expr("other_perms & ".(int)Sourcemap::READ), '>', 0)
            ->find_all();
        foreach($rows as $i => $row) {
            $results[] = $scm->kitchen_sink($row->id);
        }
        $this->results->results = $results;
        $this->results->limit = $this->limit;
        $this->results->offset = $this->offset;
        $this->results->hits_ret = count($results);
        $this->results->hits_tot = "wait for it...";
        return $this->results;
    }
}
