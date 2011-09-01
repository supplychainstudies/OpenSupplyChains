<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://docs.kohanaphp.com/about.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @see  http://docs.kohanaphp.com/about.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://docs.kohanaphp.com/about.autoloading
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
Kohana::init(array('base_url' => '/kohana/'));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
// Disabled the default file writer FirePHP so we can filter out FirePHP
// A custom file writer is included with this module
//Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
 Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	 // FirePHP Needs to be before database in order to get query data
	 'firephp'    => MODPATH.'firephp',    // FirePHP library

	 'auth'       => MODPATH.'auth',       // Basic authentication
//	 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	 'database'   => MODPATH.'database',   // Database access
//	 'image'      => MODPATH.'image',      // Image manipulation
//	 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
//	 'pagination' => MODPATH.'pagination', // Paging of results
//	 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

/**
 * Attach FirePHP to logging. be sure to enable firephp module
 */

// Exclude all FirePHP console logs from the file log...
Kohana::$log->attach(new FirePHP_Log_File(APPPATH.'logs'));
Kohana::$log->attach(new FirePHP_Log_Console());
Kohana::$log->add('FirePHP::INFO', 'FirePHP Initialized...')->write();

/**
 *  Examples of using the KO3 log class with FirePHP
 */

Kohana::$log->add('FirePHP::GROUP_START', 'Kohana FirePHP Demos...');
Kohana::$log->add('FirePHP::LOG', 'FirePHP Log...');
Kohana::$log->add('FirePHP::INFO', 'FirePHP Info...');
Kohana::$log->add('FirePHP::WARN', 'FirePHP Warn...');
Kohana::$log->add('FirePHP::ERROR', 'FirePHP Error...');

$demo = array('label'=>'FirePHP Table...',
	      'table' => array(
				array('Col 1 Heading', 'Col 2 Heading'),
				array('Row 1 Col 1', 'Row 1 Col 2'),
				array('Row 2 Col 1', 'Row 2 Col 2'),
				array('Row 3 Col 1', 'Row 3 Col 2')
			       ));

Kohana::$log->add('FirePHP::LOG', array('label' => 'Passing objects to log...',
					'object' => $demo));

Kohana::$log->add('FirePHP::TABLE', $demo);
Kohana::$log->add('FirePHP::LOG', 'FirePHP Log...');
// Not sure what dump does... ???
Kohana::$log->add('FirePHP::DUMP', array('key' => 2, 'variable' => $demo));
Kohana::$log->add('FirePHP::TRACE', 'FirePHP Trace...');
Kohana::$log->add('FirePHP::GROUP_END', '')->write();

// However, its much easier to just do this...
// All FirePHP commands are available, plus a few new ones...
Fire::log('Hi Mom!');
Fire::group('My Group')->warn('Warning!')->groupEnd();
Fire::error('UH OH! Now, look what you did it!');

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::instance()
	->execute()
	->send_headers()
	->response;

/**
 * Run the at the end of the bootstap to profile the entire application
 * Alternatively, you can extend one of the FirePHP Controllers
 */

Fire::warn('Be sure to configure FirePHP authorization for productions sites. Don\'t want just anybody viewing this stuff...');
FirePHP_Profiler::instance()
	->group('KO3 FirePHP Profiler Results:')
	->superglobals() // New Superglobals method to show them all...
	->database()
	->benchmark()
	->groupEnd();

