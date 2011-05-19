<?php
class Sourcemap_Search_Simple extends Sourcemap_Search {
    public function fetch() {
        parent::fetch();
        $scm = ORM::factory('supplychain');
        $this->results->results = $scm->limit($this->limit)
            ->offset($this->offset)->find_all()->as_array(null, true);
        return $this->results;
    }
}
