<?php
class Breadcrumbs extends Sourcemap_Breadcrumbs {

    public static $_instance;

    public static function instance() {
        if(!self::$_instance) self::$_instance = new self;
        return self::$_instance;
    }
}
