<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Modified File log writer. Excludes types specified
 */
class FirePHP_Log_File extends Kohana_Log_Writer {

	// Directory to place log files in
	protected $_directory;
	// List of excluded types
	protected $_excluded;
	// Format for log entries
	protected $_format;


	/**
	 * Creates a new file logger.
	 *
	 * @param   string  log directory
	 * @param   array   list of excluded types
	 * @return  void
	 */
	public function __construct($directory, array $config=NULL)
	{
	    if ( ! is_dir($directory) OR ! is_writable($directory))
	    {
		    throw new Kohana_Exception('Directory :dir must be writable',
			    array(':dir' => Kohana::debug_path($directory)));
	    }

	    // Determine the directory path
	    $this->_directory = realpath($directory).DIRECTORY_SEPARATOR;
	    $firephp = FirePHP_Profiler::instance();
	    if (isset($config)) $firephp->set_config($config);
	    $this->_format = $firephp->get_config('log.file.format', 'time --- type: body');
	    $this->_excluded = $firephp->get_config('log.file.exclude');
	}

	/**
	 * Writes each of the messages into the log file.
	 *
	 * @param   array   messages
	 * @return  void
	 */
	private function _write(array $messages)
	{
		// Set the monthly directory name
		$directory = $this->_directory.date('Y/m').DIRECTORY_SEPARATOR;

		if ( ! is_dir($directory))
		{
			// Create the monthly directory
			mkdir($directory, 0777, TRUE);
		}

		// Set the name of the log file
		$filename = $directory.date('d').EXT;

		if ( ! file_exists($filename))
		{
			// Create the log file
			file_put_contents($filename, Kohana::FILE_SECURITY.' ?>'.PHP_EOL);

			// Allow anyone to write to log files
			chmod($filename, 0666);
		}

		// Set the log line format
		$format = $this->_format;

		foreach ($messages as $message)
		{
			// Write each message into the log file
			file_put_contents($filename, PHP_EOL.strtr($format, $message), FILE_APPEND);
		}
	}

	/**
	 * Writes each of the messages into the log file.
	 *
	 * @param   array   messages
	 * @return  void
	 */
	public function write(array $messages)
	{
	    if ( ! isset($this->_excluded))
	    {
		$this->_write($messages);
	    }
	    else
	    {
		$filtered = array();
		foreach ($messages as $message)
		{
		    if ( ! in_array($message['type'], $this->_excluded))
		    {
			$filtered[] = $message;
		    }
		}
		$this->_write($filtered);
	    }

	}
}