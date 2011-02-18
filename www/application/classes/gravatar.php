<?php
class Gravatar {

    public static $_avatar_base = 'http://www.gravatar.com/avatar/%s?d=identicon';

    public static function avatar($email) {
        return sprintf(self::$_avatar_base, self::hash($email));
    }

    public static function hash($email) {
        return md5(strtolower(trim($email)));
    }
}
