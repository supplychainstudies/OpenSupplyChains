<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *  KO3 FirePHP and Profiler
 *  Version 0.3
 *  Last changed: 2010-11-8
 *  Based on:
 *  Fire_Profiler by dlib: http://learn.kohanaphp.com/2008/07/21/introducing-fire-profiler/
 *  KO3 conversion by ralf: http://code.goldenzazu.de/fireprofiler.php
 */

// Grab the vendor api library
require Kohana::find_file('vendor', 'FirePHP/FirePHP', $ext = 'class.php');

/**
 *  Main Class
 */
class Vendor_FirePHP extends FirePHP {
    protected $_config = array();
    protected $_queries = array();
    protected $_authorized = NULL;

    public function  __construct(array $config=Null)
    {
	//parent::__construct();
	// just in case we're referenced while initializing...
	self::$instance = $this;
	$this->_init($config);
    }

    // Shouldn't need more than one instance of this...
    final private function  __clone() {}

    protected function _init(array $config=Null)
    {
	// disable while initializing...
	$this->enabled = false;
	$this->_config = Kohana::config('firephp.default');
	$this->_config = (isset($config)) ? Arr::merge($this->_config, $config) : $this->_config;
	$this->setOptions($this->get_config('firephp', $this->getOptions()));
	$this->enabled = $this->get_enabled();
	//$this->info('FirePHP Initialized...');
    }

    public function get_config($key, $default = NULL)
    {
	return Arr::path($this->_config, $key, $default);
    }

    public function set_config($key, $value=NULL)
    {
	// only configure when enabled...
	if ($this->enabled)
	{
	    if ( ! empty($key))
	    {
		if (is_array($key)) {
		    $value = $key;
		} else {
		    // Convert dot-noted key string to an array
		    $keys = explode('.', rtrim($key, '.'));
		    // This will set a value even if it didn't previously exist
		    do {
			$key = array_pop($keys);
			$value = array($key => $value);
		    } while ($keys);
		}
		$this->_config = Arr::merge($this->_config, $value);
		$this->setOptions($this->get_config('firephp', $this->getOptions()));
		$this->enabled = $this->get_enabled();
	    }
	}
	return $this;
    }
    
    // Override parent method...
    public function setEnabled($Enabled) 
    {
	$this->set_enabled($Enabled);
    }
    
    public function set_enabled($enabled) 
    {
	return $this->set_config('enabled', $enabled);
    }

    // Override parent method...
    public function getEnabled() {
	return $this->get_enabled();
    }
    
    public function get_enabled() 
    {
	return ($this->is_authorized() AND $this->get_config('enabled', FALSE));
    }   

    public function is_authorized()
    {
	if (isset($this->_authorized)) return $this->_authorized;
	// Return TRUE if Auth is disabled
	if ($this->get_config('auth.enabled', FALSE) === FALSE) 
		return $this->_authorized = TRUE;

	// Check to see if the Auth class is available
	if (class_exists('Auth'))
	{
	    $logged_in = $this->get_config('auth.logged_in', TRUE);
	    if (($logged_in === TRUE) AND Auth::instance()->logged_in())
		    return $this->_authorized = TRUE;
	    if (is_array($logged_in))
	    {
		foreach($logged_in as $role => $val)
		{
		    if (($val === TRUE) AND Auth::instance()->logged_in($role))
			    return $this->_authorized = TRUE;
		}
		
	    }
	}
	// Not Authorized...
	return $this->_authorized = FALSE;
    }

    // Disable error and exception handling... KO3 does it for us...
    public function registerErrorHandler($throwErrorExceptions=true) {}
    public function registerExceptionHandler() {}
    // Disable assertion handler until I can verify it doesn't break anything...
    public function registerAssertionHandler($convertAssertionErrorsToExceptions=true, $throwAssertionExceptions=false) {}

    public function phpversion()
    {
	return $this->info(phpversion(), 'Current PHP version: ');
    }

    public function superglobals()
    {
	return $this->instance()
		->group('PHP Superglobals')
		->server()
		->get()
		->files()
		->post()
		->cookie()
		->session()
		->request()
		->env()
		->groupEnd();
    }

    public function server()
    {
	return (isset($_SERVER)) ? $this->variable_table($_SERVER, '$_SERVER') : $this->info('[empty]', '$_SERVER');
    }

    public function get()
    {
	return (isset($_GET)) ? $this->variable_table($_GET, '$_GET') : $this->info('[empty]', '$_GET');
    }

    public function files()
    {
	return (isset($_FILES)) ? $this->variable_table($_FILES, '$_FILES') : $this->info('[empty]', '$_FILES');
    }

    public function post()
    {
	// check for the validation object...
	if (is_object($_POST)) { 
	    $this->info($_POST, '$_POST');
	}
	return (isset($_POST)) ? $this->variable_table((array)$_POST, '$_POST') :  $this->info('[empty]', '$_POST');
    }

    public function cookie()
    {
	return (isset($_COOKIE)) ? $this->variable_table($_COOKIE, '$_COOKIE') : $this->info('[empty]', '$_COOKIE');
    }

    public function session()
    {
	return (isset($_SESSION)) ? $this->variable_table($_SESSION, '$_SESSION') : $this->info('[empty]', '$_SESSION');
    }

    public function request()
    {
	return (isset($_REQUEST)) ? $this->variable_table($_REQUEST, '$_REQUEST') : $this->info('[empty]', '$_REQUEST');
    }

    public function env()
    {
	return (isset($_ENV)) ? $this->variable_table($_ENV, '$_ENV') : $this->info('[empty]', '$_ENV');
    }


    /**
     * Benchmark times and memory usage from the Benchmark library.
     */
    public function database()
    {
	if ($this->enabled) 
	{
	    // Check to see if the Database class is available
	    if (class_exists('Database'))
	    {
		if ( ! empty($this->_queries))
		{
		    $this->group(__('Database').' '. __('Queries').': ('.count($this->_queries).')');
		    foreach ($this->_queries as $query)
		    {
			if ($query['fb'] == 'table')
			{
			    $this->multicolumn_table($query['data'], $query['sql']);
			} else {
			    $this->log($query['sql'], $query['label']);
			}
		    }
		    $this->groupEnd();
		}
		$this->benchmark('Database');
	    }
	}
	return $this;
    }

    protected function _store_query(array $query)
    {
	if (empty($query)) return false; // throw exception?
	if ($this->get_config('database.group', FALSE))
	{
	    $this->_queries[] = $query;
	}
	else
	{
	    if ($query['fb'] == 'log')
	    {
		$this->log($query['sql'], $query['label']);
	    } else {
		$this->multicolumn_table($query['data'], $query['sql']);
	    }
	}
    }

    /**
    * @param result  object   Database_Result for SELECT queries
    * @param result  mixed    the insert id for INSERT queries
    * @param result  integer  number of affected rows for all other queries
    */
    public function query($result, $type, $sql)
    {
	$store = array('fb' => 'log', 'sql' => $sql);
	$max = $this->get_config('database.rows', 10);
	if ($type === Database::SELECT)
	{
	    if ($this->get_config('database.select', FALSE))
	    {
		$rows = $result->as_array();
		$result->rewind();
		if (count($rows) > 0)
		{
		    $store['fb'] = 'table';
		    $store['data'] = array_slice($rows, 0, $max);

		}
		else
		    $store['label'] = count($rows).' '.__('rows');
	    }
	}
	elseif ($type === Database::INSERT)
	{
	    if ($this->get_config('database.insert', FALSE))
	    {
		if (count($result) > 0)
		{
		    $store['fb'] = 'table';
		    $store['data'] = array_slice($result, 0, $max);
		}
		else
		    $store['label'] = count($result).' '.__('rows');
	    }
	}
	elseif ($type === Database::UPDATE)
	{
	    if ($this->get_config('database.update', FALSE))
	    {
		$store['label'] = $result.' '.__('rows');
	    }
	}
	else
	{
	    if ($this->get_config('database.other', FALSE))
	    {
		$store['label'] = $result.' '.__('rows');
	    }
	}
    $this->_store_query($store);
    }

    public function benchmark($table = FALSE)
    {
	if ($this->enabled)
	{
	    foreach (Profiler::groups() as $group => $benchmarks)
	    {
		$tablename = ucfirst($group);
		// Exclude database unless specifically run
		if ((empty($table) AND strpos($tablename,'Database') === FALSE) OR strpos($tablename,$table) === 0)
		{
		    $row = array( array(__('Benchmark'),__('Min'),__('Max'), __('Average'),__('Total')) );
		    foreach ($benchmarks as $name => $tokens)
		    {
			$stats = Profiler::stats($tokens);
			$cell = array( $name.' (' . count($tokens).')' );
			foreach (array('min', 'max', 'average', 'total') as $key)
			{
			    $cell[] =  ' ' . number_format($stats[$key]['time'], 6). ' '. __('seconds') ;
			}
			$row[] = $cell;
		    }
		    $cell = array('');
		    foreach (array('min', 'max', 'average', 'total') as $key)
		    {
			$cell[] = ' ' . number_format($stats[$key]['memory'] / 1024, 4) . ' kb';
		    }
		    $row[] = $cell;
		    // Translate before passing...
		    $this->fb(array(__($tablename), $row ),FirePHP::TABLE);
		}
	    }

	    if (empty($table) || strpos('Application',$table) === 0)
	    {
		$stats = Profiler::application();
		$tablename = array(__('Application Execution').' ('.$stats['count'].')');
		$row = array(array('','min', 'max', 'average', 'current'));
		$cell = array('Time');
		foreach (array('min', 'max', 'average', 'current') as $key)
		{
		    $cell[] = number_format($stats[$key]['time'], 6) . ' ' .  __('seconds');
		}
		$row[] = $cell;
		$cell = array('Memory');
		foreach (array('min', 'max', 'average', 'current') as $key)
		{
		    $cell[] = number_format($stats[$key]['memory'] / 1024, 4) . ' kb';
		}
		$row[] = $cell;
		$this->fb(array($tablename, $row ),FirePHP::TABLE);
	    }
	}
	return $this;
    }

    /**
     * Creats a Key = Value Variable Style Table
     * @param array $data
     * @param <type> $label
     * @param <type> $count
     * @return <type> $this
     */
    public function variable_table(array $data, $label='', $count=TRUE)
    {
	if ($this->enabled)
	{
	    if (empty($data)) return $this->info('[empty]', $label);
	    $table = array();
	    $table[] = array(__('Key'), __('Value'));
	    foreach($data as $key => $value)
	    {
		if (is_object($value))
		{
		    $value = ($this->get_config('tables.show_objects', FALSE))
			   ? $value
			   : get_class($value).' [object]';
		}
		$table[] = array($key, $value);
	    }
	    $label = ($count) ? $label.' ('.count($data).') ' : $label;
	    $this->table($label, $table);
	}
	return $this;
    }

    /**
     * Creates a table with column headers from an associative array
     * @param array $data
     * @param string $label
     * @param bool $count (default TRUE) Counts the rows
     * @param bool $row_num (default TRUE) Numbers the rows
     */
    public function multicolumn_table(array $data, $label='', $count=TRUE, $row_num=TRUE)
    {
	if ($this->enabled)
	{
	    if (empty($data)) return $this->info('[empty]', $label);

	    $table = array();
	    foreach ($data as $num => $row)
	    {
		if ($row_num)
		{
		    if ($num === 0) $table[$num]['row_num'] = '';
		    $table[$num+1]['row_num'] = $num+1;
		}
		if (is_array($row))
		foreach ($row as $field => $value)
		{
		    if ($num === 0)
		    {
			$table[$num][$field] = $field; // header
		    }
		    if (is_object($value))
		    {
			$value = ($this->get_config('tables.show_objects', FALSE))
			       ? $value
			       : get_class($value).' [object]';
		    }
		    $table[$num+1][$field] = $value;
		}
	    }
	    $label = ($count) ? $label.' ('.count($data).') ' : $label;
	    $this->table($label, $table);
	}
    }

    public function dump($Key, $Variable, $Options = array())
    {
	parent::dump($Key, $Variable, $Options);
	return $this;
    }

    public function error($Object, $Label = null, $Options = array())
    {
	parent::error($Object, $Label, $Options);
	return $this;
    }

    public function group($Name, $Options=null)
    {
	parent::group($Name, $Options) ;
	return $this;
    }

    public function groupEnd()
    {
	parent::groupEnd();
	return $this;
    }

    public function info($Object, $Label = null, $Options = array())
    {
	parent::info($Object, $Label, $Options);
	return $this;
    }

    public function log($Object, $Label = null, $Options = array())
    {
	parent::log($Object, $Label, $Options);
	return $this;
    }

    public function table($Label, $Table, $Options = array())
    {
	parent::table($Label, $Table, $Options);
	return $this;
    }

    public function trace($Label)
    {
	parent::trace($Label);
	return $this;
    }

    public function warn($Object, $Label = null, $Options = array())
    {
	parent::warn($Object, $Label, $Options);
	return $this;
    }

    public static function instance($_config=Null)
    {
	return (self::$instance)
	    ? self::$instance->set_config($_config)
	    : new self($_config);
    }


}
