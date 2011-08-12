<?php
class Gravatar {

    public static $_avatar_base = 'http://www.gravatar.com/avatar/%s?d=%s&s=%d'; // identicon | retro

    public static function avatar($email, $sz=64, $d='mm') {
		if($d == "mm") { $d = URL::base(true, true)."assets/images/default-user.png"; }
        $sz = min(512, max(1, $sz), $sz);
        return sprintf(self::$_avatar_base, self::hash($email), urlencode($d), $sz);
    }

    public static function hash($email) {
        return md5(strtolower(trim($email)));
    }
}
