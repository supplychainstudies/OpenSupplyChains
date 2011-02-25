<?php
class Model_Iploc extends Model {
    public function find($ipaddr) {
        $decip = Sourcemap_Ip::dot2dec($ipaddr);
        $loc = false;
        if($decip) {
            $sql = sprintf(
                'select * from iploc_block where st <= %d and en >= %d', 
                $decip, $decip
            );
            $loc = $this->_db->query(Database::SELECT, $sql, true)->as_array();
        }
        return $loc;
    }
}
