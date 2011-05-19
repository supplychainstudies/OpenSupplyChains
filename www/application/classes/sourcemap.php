<?php
class Sourcemap {
    const PROJ = 3857;
    const EPSGPROJ = 'WGS84';

    const READ = 1;
    const WRITE = 2;
    const DELETE = 8;

    // supplychain flags
    const FEATURED = 8;
    const NOSTATIC = 32;

    // user flags (see model/user)
    const ACTIVE = 1;
    const VERIFIED = 64;

    // env constants
    const DEV = 'development';
    const STAGING = 'staging';
    const PRODUCTION = 'production';

    public static $session_dir = '/tmp';

    public static $_sess_save_path;

    public static $env = self::PRODUCTION;

    public static function init() {
        if(isset(Kohana::$environment))
            self::$env = Kohana::$environment;
        Cache::$default = 'default';
        Sourcemap_JS::add_packages(
            isset(Kohana::config('js')->packages) ? 
                Kohana::config('js')->packages : array()
        );
        //Sourcemap_JS::$bundle = self::$env == self::DEV ? false : true;
        Sourcemap_JS::$bundle = false;
        Sourcemap_JS::$minified = self::$env == self::DEV ? false : true;
        Sourcemap_JS::$bundle_path = 'assets/scripts/bundles/'.self::site().'/';
        Sourcemap_CSS::$convert_less = self::$env == self::DEV ? false : true;
        // Use db for session storage
        Session::$default = 'database';
    }

    public static function environment() {
        return self::$env;
    }

    public static function revision() {
        static $rev = null;
        if($rev === null) {
            $p = DOCROOT.'revision.txt';
            if(file_exists($p) && is_readable($p)) {
                $contents = @file_get_contents($p);
                if($contents) $rev = trim($contents);
                else $rev = false;
            }
        }
        return $rev;
    }

    public static function sites_path() {
        static $path;
        if(!$path) {
            $path = rtrim(
                SOURCEMAP_SITES_PATH, 
                DIRECTORY_SEPARATOR
            ).DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    public static function sites_avail() {
        static $sites;
        if(!$sites) {
            if($sites = Kohana::config('sourcemap.sites')) {
                // pass
            } else {
                $sites = array();
                $sdir = dir(self::sites_path());
                while(false !== ($f = $sdir->read())) {
                    if(substr($f, 0, 1) != '.' && is_dir(self::sites_path().$f))
                        $sites[] = $f;
                }
            }
        }
        return $sites;
    }

    public static function site($new_site=null) {
        static $site;
        if(!$site) $site = SOURCEMAP_SITE;
        if($new_site) $site = $new_site;
        return $site;
    }
}
