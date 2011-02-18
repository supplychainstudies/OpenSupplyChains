<?php
class Kohana extends Kohana_Core {

    public static function include_path() {
        return self::$_paths;
    }

    public static function add_include_path($path) {
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(!in_array($path, self::$_paths)) {
            array_unshift(self::$_paths, $path);
        }
        return true;
    }

    public static function remove_include_path($path) {
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(in_array($path, self::$_paths)) {
            if(false !== ($i = array_search($path, self::$_paths))) {
                array_splice(self::$_paths, $i, 1);
            }
        }
        return true;
    }

}
