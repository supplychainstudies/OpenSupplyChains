<?php
class Sourcemap_Beanstalk_Client {

    protected $_conxn = null;
    protected $_host = '127.0.0.1';
    protected $_port = 11300;

    protected $_errors = null;

    protected $_persist = false;

    const MAXJOBSZ = 65536;

    public function __construct($host, $port) {
        $this->_host = $host;
        $this->_port = $port;
        $this->connect();
    }
    public function connect() {
        if(!$this->_persist || !$this->_conxn) $this->disconnect();
        if($this->_persist) {
            if($this->_conxn) {
                return true;
            } else {
                $this->_conxn = @pfsockopen($this->_host, $this->_port);
            }
        } else {
            $this->_conxn = @fsockopen($this->_host, $this->_port);
        }
        return $this->_conxn;
    }

    public function disconnect() {
        $this->_errors = array();
        unset($this->_conxn);
    }

    protected function _read() {
        return fread($this->_conxn, self::MAXJOBSZ);
    }

    protected function _readln() {
        return fgets($this->_conxn, self::MAXJOBSZ);
    }

    protected function _write($payload) {
        return fwrite($this->_conxn, $payload, self::MAXJOBSZ);
    }

    public function errors() {
        return $this->_errors;
    }
}
