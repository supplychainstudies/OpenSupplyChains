<?php
class Dotorg_Archive {

    public $archive_path;

    public static function instance() {
        static $instance = null;
        if(!$instance) {
            $instance = new Dotorg_Archive();
        }
        return $instance;
    }

    public function __construct($p=null) {
        $this->archive_path = dirname(__FILE__).'/_archive/';
        if($p) $this->archive_path = $p;
    }

    public function file_list($g='*.converted.json') {
        $fs = glob($this->archive_path.$g);
        return $fs; 
    }

    public function by_userid($userid) {
        $userid = (int)$userid;
        $fl = $this->file_list('*.migrate.json');
        $objs = array();
        foreach($fl as $i => $f) {
            $ob = json_decode(file_get_contents($f));
            $oid = null;
            foreach($ob as $oid => $obj) {
                break;
            }
            if((int)$obj->creator === $userid)
                $objs[] = $obj->oid;
        }
        return $objs;
    }

    public function by_username($username) {
        $fl = $this->file_list('*.migrate.json');
        $objs = array();
        foreach($fl as $i => $f) {
            $ob = json_decode(file_get_contents($f));
            $oid = null;
            foreach($ob as $oid => $obj) {
                break;
            }
            if($obj->user->name === $username)
                $objs[] = $obj->oid;
        }
        return $objs;
    }

    public function load_obj($oid) {
        $fn = $this->archive_path."$oid.sm.migrate.json";
        $obj = @json_decode(file_get_contents($fn));
        if(!$obj) return false;
        foreach($obj as $oid => $ob) {
            break;
        }
        return $ob;
    }

    public function load_sc($oid) {
        $fn = $this->archive_path."$oid.sm.converted.json";
        $sc = @json_decode(file_get_contents($fn));
        return $sc;
    } 

    public function get_details($oids) {
        if(!is_array($oids)) $oids = array($oids);
        $details = array();
        foreach($oids as $i => $oid) {
            $oldfn = "$oid.sm.migrate.json";
            $newfn = "$oid.sm.converted.json";
            $obj = $this->load_obj($oid);
            $sc = $this->load_sc($oid);
            if($obj && $sc) {
                $details[$oid] = (object)array(
                    'title' => $obj->name,
                    'creator' => $obj->creator,
                    'slug' => $obj->slug,
                    'oid' => $obj->oid,
                    'created' => $obj->timecreated,
                    'oldurl' => "http://sourcemap.org/{$obj->type}/{$obj->slug}"
                );
            }
        }
        return $details;
    }
}
