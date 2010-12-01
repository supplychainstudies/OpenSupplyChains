<?php
class Message extends Sourcemap_Message {
    
    static $_instance;

    public static function instance() {
        if(!self::$_instance) self::$_instance = new self;
        return self::$_instance;
    }
}
