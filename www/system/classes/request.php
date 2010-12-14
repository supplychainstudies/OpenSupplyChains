<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {
    public static $raw_req_body;

    public static function instance(& $uri = true) {
        if(!self::$instance)
            self::$raw_req_body = @file_get_contents('php://input');
        return parent::instance($uri);
    }
}
