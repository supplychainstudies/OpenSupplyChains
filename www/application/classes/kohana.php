<?php
class Kohana extends Kohana_Core {

    public static function add_include_path($path) {
        if(!in_array($path, self::$_paths)) {
            array_unshift(self::$_paths, $path);
        }
        return true;
    }

}
