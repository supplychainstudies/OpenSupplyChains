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

class Sourcemap {
    const PROJ = 3857;
    const EPSGPROJ = 'WGS84';

    const READ = 1;
    const WRITE = 2;
    const DELETE = 8;

    // supplychain flags
    const FEATURED = 8;
    #const NOSTATIC = 32;

    // user flags (see model/user)
    const ACTIVE = 1;
    const VERIFIED = 64;

    // add'l comment flags
    const ABUSE = 2;
    const HIDDEN = 4;

    // env constants
    const DEV = 'development';
    const STAGING = 'staging';
    const PRODUCTION = 'production';

    public static $admin_email = 'admin@sourcemap.com';

    public static $session_dir = '/tmp';

    public static $_sess_save_path;

    public static $env = self::PRODUCTION;

    public static $job_queue = null;

    public static $plugins = null;

    public static function init() {
        if(isset(Kohana::$environment))
            self::$env = Kohana::$environment;
        Cache::$default = 'default';
        Sourcemap_JS::add_packages(
            isset(Kohana::config('js')->packages) ? 
                Kohana::config('js')->packages : array()
        );
        //Sourcemap_JS::$bundle = self::$env == self::DEV ? false : true;
        Sourcemap_JS::$bundle = Kohana::config('sourcemap.bundle');
        Sourcemap_JS::$minified = self::$env == self::DEV ? false : true;
        Sourcemap_JS::$bundle_path = 'assets/scripts/bundles/'.self::site().'/';
        Sourcemap_CSS::$convert_less = self::$env == self::DEV ? false : true;
        // Use db for session storage
        Session::$default = 'database';
        self::$plugins = array();
    }

    public static function shutdown() {
        foreach(self::$plugins as $nm => $p) {
            try {
                $p->shutdown();
            } catch(Exception $e) {
                // log this?
            }
        }
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

    public static function assets_path() {
        //TODO: make this configurable
        static $assets_path;
        if(!$assets_path)
            $assets_path = dirname(dirname(dirname(__FILE__))).'/assets/';
        return $assets_path;
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

    public static function site_path() {
        return self::sites_path().self::site().DIRECTORY_SEPARATOR;
    }

    public static function enqueue($type, $params=null) {
        if(Kohana::config('sourcemap.job_queue')) {
            if(!self::$job_queue) {
                Sourcemap::$job_queue = Sourcemap_Job_Queue::instance(
                    Kohana::config('sourcemap.job_queue_host'), 
                    Kohana::config('sourcemap.job_queue_port')
                );
            }
            $job = Sourcemap_Job::factory($type, $params);
            return Sourcemap::$job_queue->enqueue($job);
        }
        return false;
    }

    public static function fmt_date($t) {
        return date('%M %j, %Y', $t);
    }

    public static function plugin_paths() {
        static $plugin_paths;
        if(!$plugin_paths) {
            $plugin_paths = array();
            if(is_dir(self::site_path().'plugins'.DIRECTORY_SEPARATOR)) {
                $plugin_paths[] = self::site_path().'plugins'.DIRECTORY_SEPARATOR;
            }
            if(is_dir(APPPATH.'plugins'.DIRECTORY_SEPARATOR)) {
                $plugin_paths[] = APPPATH.'plugins'.DIRECTORY_SEPARATOR;
            }
        }
        return $plugin_paths;
    }

    public static function plugins_avail() {
        static $plugins;
        if(!$plugins) {
            if($plugins = Kohana::config('sourcemap.plugins')) {
                // pass
            } else {
                $plugins = array();
                $paths = self::plugin_paths();
                foreach($paths as $pi => $path) {
                    $sdir = dir($path);
                    while(false !== ($f = $sdir->read())) {
                        if(substr($f, 0, 1) != '.' && is_dir($path.$f))
                            $plugins[$f] = $path.$f.DIRECTORY_SEPARATOR;
                    }
                }
            }
        }
        return $plugins;
    }

    public static function register_plugin($plugin_name) {
        $avail = self::plugins_avail();
        if(isset($avail[$plugin_name])) {
            if(isset(self::$plugins[$plugin_name])) {
                // pass?
            } else {
                $plugin_path = $avail[$plugin_name];
                $plugin = new Sourcemap_Plugin($plugin_name, $plugin_path);
                self::$plugins[$plugin_name] = $plugin;
                Kohana::add_include_path($plugin->path);
                $bootstrap_path = "$plugin_path/bootstrap.php";
                if(file_exists($bootstrap_path)) {
                    include($bootstrap_path);
                }
            }
        }
        return;
    }

    public static function unregister_plugin($plugin_name) {
        if(isset(self::$plugins[$plugin_name])) {
            $plugin = self::$plugins[$plugin_name];
            $teardown_path = "{$plugin->path}/teardown.php";
            if(file_exists($teardown_path)) {
                include($teardown_path);
            }
            unset(self::$plugins[$plugin_name]);
            Kohana::remove_include_path($plugin->path);
            unset($plugin);
        }
        return;
    }

    public static function get_plugin($plugin_name) {
        $plugin = null;
        if(isset(self::$plugins[$plugin_name])) {
            $plugin = self::$plugins[$plugin_name];
        }
        return $plugin;
    }
}
