<?php
class Sourcemap_Upload {
    public $php_key = null;
    public $name = null;
    public $tmp_name = null;
    public $error = null;
    public $size = 0;

    protected $_saved = false;

    public static function upload_dir($sdir=null) {
        if($sdir) $sdir = ltrim($sdir, DIRECTORY_SEPARATOR);
        else $sdir = '';
        return Kohana::config('sourcemap')->upload_dir.$sdir;
    }

    public static function get_uploads() {
        static $uploads = null;
        if(!is_array($uploads)) {
            $uploads = array();
            foreach($_FILES as $k => $v) {
                $uploads[$k] = new Sourcemap_Upload($k);
            }
        }
        return $uploads;
    }

    public function __construct($php_key) {
        if($php_key && isset($_FILES[$php_key])) {
            $finfo = $_FILES[$php_key];
            $this->php_key = $php_key;
            foreach($finfo as $k => $v) $this->{$k} = $v;
        }
    }

    public function as_array() {
        return (array)$this;
    }

    public function ok() {
        return !$this->error;
    }

    public function save_as($filename, $dir=null) {
        $dir = self::upload_dir($dir);
        $ret = Upload::save($this->as_array(), $filename, $dir);
        if($ret) $this->_saved = $ret;
        else throw new Exception(
            "Could not save file \"{$this->php_key}\" as \"{$filename}\"."
        );
        return $ret;
    }

    public function get_contents() {
        return file_get_contents($this->tmp_name);
    }
}
