<?php
class Sourcemap_Beanstalk {

    protected $_conxn = null;
    protected $_host = null;
    protected $_port = null;

    protected $_errors = null;

    public function __construct($host, $port) {
        $this->_host = $host;
        $this->_port = $port;
    }
    public function connect() {
        if($this->_conxn) $this->disconnect();
        // connect
    }

    public function disconnect() {
        // disconnect
    }

    public function errors() {
        return $this->_errors;
    }

    public function put() {
    
    }

    public function delete() {
    
    }

    public function uze($tube=null) {
    
    }
}
