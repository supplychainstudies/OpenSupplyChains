<?php
class Sourcemap_Job {

    const STATICMAPGEN = 'staticmapgen';

    public $type = null;
    public $data = null;

    public function __toString() {
        return $this->get_seialized_data();
    }

    public static function factory($type, $data=null) {
        $cls = 'Sourcemap_Job_'.ucfirst($type);
        $rc = new ReflectionClass($cls);
        $inst = $rc->newInstance();
        $inst->type = $type;
        $inst->data = $data;
        return $inst;
    }

    public function get_serialized_data() {
        $json = json_encode(array(
            'type' => $this->type, 'data' => $this->data
        ));
        return $json;
    }

}
