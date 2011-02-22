<?php
class Sourcemap_Event {

    protected $type = 'generic';
    protected $when = null;

    public static function factory($type=null, $data=null) {
        if($type) {
            $cls = 'Sourcemap_Event_'.
                preg_replace('/\s+/', '_', ucwords(str_replace('_', ' ', $type)));
            $rc = new ReflectionClass($cls);
            $evt = $rc->newInstance($data);
        } else {
            $evt = new self();
        }
        return $evt;
    }
}
