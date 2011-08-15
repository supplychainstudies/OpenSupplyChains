<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/Chicago');

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
Kohana::modules(array(
	'auth'       => MODPATH.'auth',       // Basic authentication
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
	'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'oauth'      => MODPATH.'oauth',      // OAuth authentication
	'pagination' => MODPATH.'pagination' // Paging of results
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

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
        $response = <<<HTML
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Sourcemap :: Page not found.</title>
                    <link href="assets/styles/reset.css" rel="stylesheet" type="text/css" />
                    <link href="assets/styles/general.css" rel="stylesheet" type="text/css" />
                    <style>
                        body > * {
                            padding: .5em;
                            font-family: Helvetica, Arial, sans-serif;
                        }
                        header {
                            font-size: 1.2em;
                            color: #332;
                        }
                        .article-content p {
                            margin: .25em;
                            margin-bottom: .5em;
                            clear: both;
                        }
                        .article-content img {
                            float: left;
                            clear: none;
                            width: 50%;
                            max-width: 515px;
                        }
                        .article-content div.gigantic {
                            font-size: 1800%;
                            font-weight: bold;
                            float: right;
                            max-width: 50%;
                        }
                    </style>
                </head>
                <body>
                    <header id="masthead">
                        <h1>You've Strayed into Uncharted Waters</h1>
                    </header>
                    <div class="article-content">
                        <div class="gigantic">404</div>
                        <img src="assets/images/monsters.png" />
                        <p>This is the part of the map where it says, "Here be monsters."</p><p>Please, <a href="/">go back</a>.</p>
                        <p>If you have questions or concerns, <a href="mailto:support@sourcemap.com">contact us</a>.</p>
                    </div>
                    <!--
                        <?= $e ?>
                    -->
                </body>
            </html>
HTML;
    }
    echo $response;
    exit;
    /*echo Request::instance()
        ->execute()
        ->send_headers()
        ->response;
    */
}
