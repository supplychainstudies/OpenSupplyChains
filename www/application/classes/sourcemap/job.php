<?php
class Sourcemap_Job {

    const STATICMAPGEN = 'staticmapgen';

    public $type = null;
    public $data = null;

    public static function factory($type, $data=null) {
        $cls = 'Sourcemap_Job_'.ucfirst($type);
        $rc = new ReflectionClass($cls);
        $inst = $rc->newInstance();
        $inst->data = $data;
        return $inst;
    }
}
