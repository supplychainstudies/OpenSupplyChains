<?php
class Sourcemap_Job_Queue {

    public static $_instances = array();

    public static function instance($host, $port) {
        if(!isset(self::$_instances[$host][$port])) {
            if(!isset(self::$_instances[$host])) self::$_instances[$host] = array();
            $inst = new Sourcemap_Job_Queue();
            $inst->host = $host;
            $inst->port = $port;
            self::$_instances[$host][$port] = $inst;
        }
        return self::$_instances[$host][$port];
    }

    public $host = '127.0.0.1';
    public $port = 11254;

    public function enqueue(Sourcemap_Job $job) {
        // put -> beanstalkd
    }
}
