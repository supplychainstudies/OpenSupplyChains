<?php defined('SYSPATH') or die('No direct script access.');
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
 * program. If not, see <http://www.gnu.org/licenses/>. */

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/New_York');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------


/**
 * Set Kohana::$environment if $_ENV['KOHANA_ENV'] has been supplied.
 * 
 */
if(getenv('SOURCEMAP_ENV')) {
    Kohana::$environment = getenv('SOURCEMAP_ENV');
} else {
    Kohana::$environment = Sourcemap::PRODUCTION;
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
    'cache_dir' => CACHEPATH,
    'index_file' => ''
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
//Kohana::$log->attach(new Kohana_Log_File(LOGPATH));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);
if(isset(Kohana::config('sourcemap')->base_url)) {
    Kohana::$base_url = Kohana::config('sourcemap')->base_url;
}
if(isset(Kohana::config('sourcemap')->cache_dir)) {
    Kohana::$cache_dir = Kohana::config('sourcemap')->cache_dir;
}

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */  

if(Kohana::config('sourcemap.debug')) {     
  Kohana::modules(array(  
    'firephp'   => MODPATH.'firephp', //FIREphp debug extension 
    'auth'       => MODPATH.'auth',       // Basic authentication
    'cache'      => MODPATH.'cache',      // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'database'   => MODPATH.'database',   // Database access
    // 'image'      => MODPATH.'image',      // Image manipulation
    'orm'        => MODPATH.'orm',        // Object Relationship Mapping
    // 'oauth'      => MODPATH.'oauth',      // OAuth authentication
    'pagination' => MODPATH.'pagination', // Paging of results
    // 'unittest'   => MODPATH.'unittest',   // Unit testing
    // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    'sitemap' => MODPATH.'sitemap',
    'recaptcha' => MODPATH.'recaptcha' //RECAPTCHA for Kohona,
    ));
} else {
   	Kohana::modules(array(  
	    //'firephp'   => MODPATH.'firephp', //FIREphp debug extension 
	    'auth'       => MODPATH.'auth',       // Basic authentication
	    'cache'      => MODPATH.'cache',      // Caching with multiple backends
	    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	    'database'   => MODPATH.'database',   // Database access
	    // 'image'      => MODPATH.'image',      // Image manipulation
	    'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	    // 'oauth'      => MODPATH.'oauth',      // OAuth authentication
	    'pagination' => MODPATH.'pagination', // Paging of results
	    // 'unittest'   => MODPATH.'unittest',   // Unit testing
	    // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	    'sitemap' => MODPATH.'sitemap',
	    'recaptcha' => MODPATH.'recaptcha' //RECAPTCHA for Kohona,
	    )); 
}
Kohana::add_include_path(SOURCEMAP_SITES_PATH.SOURCEMAP_SITE.'/');

if(is_file(SOURCEMAP_SITES_PATH.SOURCEMAP_SITE.'/bootstrap'.EXT)) {
    require SOURCEMAP_SITES_PATH.SOURCEMAP_SITE.'/bootstrap'.EXT;
}

if(is_dir(SOURCEMAP_SITES_PATH.SOURCEMAP_SITE.'/config/')) {
    $site_config_dir = SOURCEMAP_SITES_PATH.SOURCEMAP_SITE.'/config/';
    Kohana::$config->attach(new Kohana_Config_File($site_config_dir));
}

Sourcemap::init();

/**
 * Set the default (all sites) routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI. May be overridden in sites/<site>/bootstrap.php.
 */

if(!defined('SUPPRESS_DEFAULT_ROUTES')) {
    Route::set('services', 'services(/<controller>(/<id>(.<format>)))')
        ->defaults(array(
            'directory' => 'services', 'controller' => 'services', 
            'action' => 'index'
        ));

    Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'welcome',
            'action'     => 'index',
        ));
}

isset(Kohana::config('sourcemap')->base_url) ? $base_url = Kohana::config('sourcemap')->base_url : $base_url = NULL;

$styles = array(
    'assets/styles/general.less',
    'sites/default/assets/styles/reset.css'
    );
$header_style = isset($styles) ? Sourcemap_CSS::link_tags($styles) : '';
$header = View::factory('partial/branding', array('page_title' => isset($page_title) ? $page_title : APPLONGNM));
$footer = View::factory('partial/footer', array('page_title' => isset($page_title) ? $page_title : APPLONGNM));
$scripts = Sourcemap_JS::script_tags('less', 'sourcemap-core');

if (!defined('SUPPRESS_REQUEST')) {
    /**
     * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
     * If no source is specified, the URI will be automatically detected.
     */
    try {
        $response = Request::instance()
            ->execute()
            ->send_headers()
            ->response;

    } catch(Exception $e) {
        if(Kohana::config('sourcemap.debug')) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: text/plain');
            die($e);
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    	$e = addcslashes($e,"\\\'\"&\n\r<>:"); ;
        $response = <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>Page Not Found</title>
<base href="$base_url" />
<style>
    body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;}
    h1{text-align:center;font-size:300%;}
    .article-content{padding-top:160px;position:relative}
    .article-content p{text-align:center;line-height:1.5em;
    margin:.25em;margin-bottom:.5em;clear:both;}
    .article-content div.gigantic{text-align:center;color:#bbb;font-size:1800%;font-weight:bold;margin:0 auto;position:relative}
    .article-content img{position:absolute;top:0;left:0;right:0;margin:0 auto;}
    .article-content div.article-img{height:170px;}
</style>
$header_style
</head>
<body>
<div id="wrapper">
    $header
    <h1>You've Strayed into Uncharted Waters</h1>
    <div class="article-content">
        <div class="article-img">
    	    <div class="gigantic">404</div>
    	    <img src="http://www.sourcemap.com/assets/images/monsters.png" />
        </div>
    	<p>This is the part of the map where it says, "Here be monsters." Please, <a href="javascript:history.go(-1)">go back</a>, and if you have questions or concerns, <a href="mailto:support@sourcemap.com">contact us</a>.</p>
    </div>
    <div class="push"></div>
</div><!-- #wrapper -->
<div id="footer">
    $footer
</div>
$scripts
<script type="text/javascript"> if (window.console) { var error = "$e"; console.log(error); } </script>
</body>
</html>
HTML;
    }


    echo $response;
    exit;
}
