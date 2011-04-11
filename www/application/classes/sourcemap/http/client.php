<?php
class Sourcemap_Http_Client { // cUrl library wrapper.

    public $url = null;
    public $method = null;
    protected $_ch = null;
    public $raw_response = null;
    public $user_agent = null;

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const HEAD = 'HEAD';
    const TRACE = 'TRACE';
    const CONNECT = 'CONNECT';


    public function __construct($url=null) {
        $this->url = $url;
        $this->method = self::GET;
        $this->user_agent = sprintf('Sourcemap HTTP Client (%d)', Sourcemap::revision());
        $this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
    }

    public function execute() {
        if(!$this->url) return false;
        curl_setopt($this->_ch, CURLOPT_URL, $this->url);
        $method = self::GET;
        switch($this->method) {
            case self::GET:
            case self::POST:
            case self::PUT:
            case self::DELETE:
            case self::HEAD:
            case self::TRACE:
            case self::CONNECT:
                $method = $this->method;
                break;
            default:
                break;
        }
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->user_agent);
        $this->raw_response = curl_exec($this->_ch);
        $this->response = Sourcemap_Http_Response::factory($this->raw_response);
        return $this->response;
    }

    public static function do_get($url) {
        $client = new self($url);
        try {
            $response = $client->execute();
        } catch(Exception $e) {
            throw new Exception('Could not fetch: '.$url.':'.$e);
        }
        return $response;
    }
}
