<?php
class Sourcemap_JS {

    public static $pkgs = null;

    public static function init() {
        if(self::$pkgs) return;
        self::add_packages(
            isset(Kohana::config('js')->packages) ? 
                Kohana::config('js')->packages : array()
        );
    }

    public static function add_packages($pkgs) {
        foreach($pkgs as $pkg_tag => $pkg_data) {
            if(!isset($pkg_data['scripts'])) continue;
            self::$pkgs[$pkg_tag] = $pkg_data;
        }
    }

    public static function get_package($pkg_tag) {
        if(!self::$pkgs) self::init();
        $package = array();
        $q = array($pkg_tag);
        while($pkg = array_pop($q)) {
            if(!isset(self::$pkgs[$pkg]))
                throw new Exception('Invalid javascript package: '.$pkg);
            if(isset(self::$pkgs[$pkg]['env'])) {
                $envs = is_array(self::$pkgs[$pkg]['env']) ? 
                    self::$pkgs[$pkg]['env'] : array(self::$pkgs[$pkg]['env']);
                if(!in_array(Sourcemap::$env, $envs)) {
                    continue;
                }
            }
            if(!in_array($pkg, $package))
                array_unshift($package, $pkg);
            $pkg = self::$pkgs[$pkg];
            if(isset($pkg['requires']) && is_array($pkg['requires'])) {
                foreach($pkg['requires'] as $ri => $req) {
                    if(!in_array($req, $q))
                        array_unshift($q, $req);
                }
            }
        }
        return $package;
    }

    public static function get_package_scripts($pkg_tag) {
        $package = self::get_package($pkg_tag);
        $scripts = array();
        foreach($package as $pi => $p) {
            if(!isset(self::$pkgs[$p]['scripts']))
                throw new Exception('Invalid javascript package: '.$pkg_tag);
            foreach(self::$pkgs[$p]['scripts'] as $si => $script) {
                $scripts[] = $script;
            }
        }
        return $scripts;
    }

    public static function scripts() {
        $args = func_get_args();
        $pkgs = array();
        foreach($args as $i => $arg) {
            if(is_array($arg)) {
                foreach($arg as $j => $a)
                    $pkgs[] = $a;
            } else $pkgs[] = $arg;
        }
        $scripts = array();
        foreach($pkgs as $pi => $pkg) {
            $scripts[] = self::get_package_scripts($pkg);
        }
        $scripts = array_unique(call_user_func_array('array_merge', $scripts));
        return $scripts;
    }

    public static function script_tags() {
        $args = func_get_args();
        $scripts = call_user_func_array(array('self', 'scripts'), $args);
        $tags = array();
        foreach($scripts as $i => $script) $tags[] = HTML::script($script);
        return join("\n", $tags);
    }
}
