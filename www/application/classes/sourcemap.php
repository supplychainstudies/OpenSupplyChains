<?php
class Sourcemap {
    const PROJ = 3857;

    const READ = 1;
    const WRITE = 2;
    const DELETE = 8;

    public static $session_dir = '/tmp';

    public static $_sess_save_path;

    public static function init() {
        session_set_save_handler(
            array('Sourcemap', "_sess_open"), array('Sourcemap', "_sess_close"), 
            array('Sourcemap', "_sess_read"), array('Sourcemap', "_sess_write"), 
            array('Sourcemap', "_sess_destroy"), array('Sourcemap', "_sess_gc")
        );
    }

    public static function _sess_open($save_path, $session_name) {
        $sess_save_path = self::$_sess_save_path = $save_path ? $save_path : self::$session_dir;
        return(true);
    }

    public static function _sess_close() {
        return(true);
    }

    public static function _sess_read($id) {
        $sess_save_path = self::$_sess_save_path;

        $sess_file = "$sess_save_path/sess_$id";
        return (string) @file_get_contents($sess_file);
    }

    public static function _sess_write($id, $sess_data) {
        $sess_save_path = self::$_sess_save_path;

        $sess_file = "$sess_save_path/sess_$id";
        if ($fp = fopen($sess_file, "w")) {
            $return = fwrite($fp, $sess_data);
            fclose($fp);
            return $return;
        } else {
            return(false);
        }

    }

    public static function _sess_destroy($id) {
        $sess_save_path = self::$_sess_save_path;
        $sess_file = "$sess_save_path/sess_$id";
        return(unlink($sess_file));
    }

    public static function _sess_gc($maxlifetime) {
        $sess_save_path = self::$_sess_save_path;
        foreach (glob("$sess_save_path/sess_*") as $filename) {
            if (filemtime($filename) + $maxlifetime < time()) {
                unlink($filename);
            }
        }
        return true;
    }


}
