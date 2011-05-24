<?php
class Sourcemap_Job_Queue {

    public static $_instances = array();

    public static function instance($host, $port) {
        if(!isset(self::$_instances[$host][$port])) {
            if(!isset(self::$_instances[$host])) self::$_instances[$host] = array();
            $inst = new Sourcemap_Job_Queue($host, $port);
            self::$_instances[$host][$port] = $inst;
        }
        return self::$_instances[$host][$port];
    }

    public $host = '127.0.0.1';
    public $port = 11300;

    public function __construct($host=null, $port=null) {
        $this->client = new Sourcemap_Beanstalk_Producer($host, $port);
        $this->host = $host;
        $this->port = $port;
    }

    public function enqueue(Sourcemap_Job $job) {
        return $this->client->put($job->get_serialized_data());
    }
}
