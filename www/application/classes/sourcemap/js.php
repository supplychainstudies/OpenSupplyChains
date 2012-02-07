<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

// TODO: make configurable at init(), rather than loading config files...?
class Sourcemap_JS {

    const EXT = '.js';
    const MIN_EXT = '.min.js';

    const BUNDLE_EXT = '.bundle.js';
    const BUNDLE_MIN_EXT = '.bundle.min.js';

    public static $pkgs = null;

    public static $bundle = false;
    public static $bundle_path = 'assets/scripts/bundles/';
    public static $minified = false;

    public static function reset_packages() {
        self::$pkgs = array();
    }

    public static function add_packages($pkgs) {
        if(self::$pkgs === null) self::$pkgs = array();
        foreach($pkgs as $pkg_tag => $pkg_data) {
            if(!isset($pkg_data['scripts'])) continue;
            self::$pkgs[$pkg_tag] = $pkg_data;
        }
    }

    public static function get_package($pkg_tag) {
        $package = array();
        $q = array($pkg_tag);
        while($pkg = array_pop($q)) {
            if(!isset(self::$pkgs[$pkg]))
                throw new Exception('Invalid javascript package: '.$pkg);
            if(isset(self::$pkgs[$pkg]['env'])) {
                $envs = is_array(self::$pkgs[$pkg]['env']) ? 
                    self::$pkgs[$pkg]['env'] : array(self::$pkgs[$pkg]['env']);
                if(!in_array(Sourcemap::environment(), $envs)) {
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

    public static function packages() {
        $args = func_get_args();
        $pkgs = array();
        foreach($args as $i => $arg) {
            if(is_array($arg)) {
                foreach($arg as $j => $a)
                    $pkgs[] = self::get_package($a);
            } else $pkgs[] = self::get_package($arg);
        }
        $pkgs = array_values(array_unique(call_user_func_array('array_merge', $pkgs)));
        return $pkgs;
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
        if(self::$bundle) {
            $pkgs = self::flatten($args);
            $scripts = array();
            foreach($pkgs as $si => $pkg) {
                $pscripts = self::get_package_scripts($pkg);
                foreach($pscripts as $pi => $ps) {
                    if(preg_match('/^http/', $ps)) {
                        if(!in_array($ps, $scripts)) $scripts[] = $ps;
                    }
                }
                $scripts[] = self::$bundle_path.$pkg.(self::$minified ? self::BUNDLE_MIN_EXT : self::BUNDLE_EXT);
            }
        } else {
            $pkgs = array();
            foreach($args as $i => $arg) {
                if(is_array($arg)) {
                    foreach($arg as $j => $a)
                        $pkgs[] = $a;
                } else $pkgs[] = $arg;
            }
            $scripts = array();
            foreach($pkgs as $pi => $pkg) {
                $pscripts = self::get_package_scripts($pkg);
                foreach($pscripts as $pi => $ps) {
                    if(preg_match('/^http/', $ps)) {
                        $scripts[] = $ps;
                    } else {
                        if(self::$minified) {
                            $scripts[] = dirname($ps).'/'.basename($ps, self::EXT).self::MIN_EXT;
                        } else {
                            $scripts[] = $ps;
                        }
                    }
                }
            }
            //if(!$scripts) $scripts[] = array();
            //$scripts = array_values(array_unique($scripts));
        }
        
        // jQuery has priority over other scripts, then Openlayers
        $scripts = self::move_to_top($scripts, "OpenLayers");
        $scripts = self::move_to_top($scripts, "jquery.js");
       
        return $scripts;
    }

    public static function move_to_top($array, $string){
        foreach ($array as $i => $value){
            if (strpos($value,$string)){
                // Pop value off array and put it on top
                array_splice($array, $i, 1);
                array_unshift($array, $value);
            }
        }
        return $array;
    }

    public static function script_tags() {
        $args = func_get_args();
        $scripts = call_user_func_array(array('self', 'scripts'), $args);
        $tags = array();
        if($rev = Sourcemap::revision()) $rev = "_v=$rev";
        else $rev = '';
        foreach($scripts as $i => $script) {
            if($rev && !preg_match('/^https?:\/\//', $script)) {
                if(strstr($script, '?')) {
                    $script .= "&$rev";
                } else {
                    $script .= "?$rev";
                }
            }
            $tags[] = HTML::script($script);
        }
        return join("\n", $tags);
    }

    public static function flatten() {
        $args = func_get_args();
        $flat = array();
        foreach($args as $i => $arg) {
            if(is_array($arg)) {
                $f = call_user_func_array(array('self', 'flatten'), $arg);
                foreach($f as $j => $ff) $flat[] = $ff;
            } else $flat[] = $arg;
        }
        return $flat;
    }

}
