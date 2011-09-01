<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Modified File log writer. Excludes types specified
 */ 
// Copyright (c) 2006-2010, Christoph Dorn
// All rights reserved.
// 
// Redistribution and use in source and binary forms, with or without modification,
// are permitted provided that the following conditions are met:
// 
//     * Redistributions of source code must retain the above copyright notice,
//       this list of conditions and the following disclaimer.
// 
//     * Redistributions in binary form must reproduce the above copyright notice,
//       this list of conditions and the following disclaimer in the documentation
//       and/or other materials provided with the distribution.
// 
//     * Neither the name of Christoph Dorn nor the names of its
//       contributors may be used to endorse or promote products derived from this
//       software without specific prior written permission.
// 
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
// ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
// WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
// DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
// ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
// LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
// ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
// SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 

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