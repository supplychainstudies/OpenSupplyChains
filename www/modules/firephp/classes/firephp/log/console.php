<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FirePHP log writer.
 */

class FirePHP_Log_Console extends Kohana_Log_Writer {
    protected $_firephp;
    // List of excluded types
    protected $_excluded;
    // Format for console entries
    protected $_format;

    /**
     * Creates a new FirePHP logger.
     *
     * @param   string  config array
     * @return  void
     */
    public function __construct(array $config=NULL) {
	$this->_firephp = FirePHP_Profiler::instance();
	if (isset($config)) $this->_firephp->set_config($config);
	$this->_format = $this->_firephp->get_config('log.console.format', 'time --- type: body');
	$this->_excluded = $this->_firephp->get_config('log.console.exclude');
    }

    /**
     * Writes each of the messages to FirePHP
     *
     * @param   array   messages
     * @return  void
     */
    public function write(array $messages) {
	// Set the log line format
	$format = $this->_format;
	foreach ($messages as $message) { // Write each message into the log file
	    if (isset($this->_excluded) AND in_array($message['type'], $this->_excluded)) continue;
	    switch($message['type'])
	    {
		/**
		* Firebug LOG level
		*/
		case 'FirePHP::LOG':
		    if (is_array($message['body']))
		    {
			$label = Arr::path($message, 'body.label', '');
			$object = Arr::path($message, 'body.object', $message['body']);
			$this->_firephp->log($object, $label);
		    } 
		    else
		    {
			$this->_firephp->log(strtr($format, $message));
		    }
		    break;
		/**
		* Firebug INFO level
		*/
		case 'FirePHP::INFO':
		    if (is_array($message['body']))
		    {
			$label = Arr::path($message, 'body.label', '');
			$object = Arr::path($message, 'body.object', $message['body']);
			$this->_firephp->info($object, $label);
		    }
		    else
		    {
			$this->_firephp->info(strtr($format, $message));
		    }
		    break;
		/**
		* Firebug WARN level
		*/
		case 'FirePHP::WARN':
		    if (is_array($message['body']))
		    {
			$label = Arr::path($message, 'body.label', '');
			$object = Arr::path($message, 'body.object', $message['body']);
			$this->_firephp->warn($object, $label);
		    }
		    else
		    {
			$this->_firephp->warn(strtr($format, $message));
		    }
		    break;
		/**
		* Firebug ERROR level
		*/
		case 'FirePHP::ERROR':
		    if (is_array($message['body']))
		    {
			$label = Arr::path($message, 'body.label', '');
			$object = Arr::path($message, 'body.object', $message['body']);
			$this->_firephp->error($object, $label);
		    }
		    else
		    {
			$this->_firephp->error(strtr($format, $message));
		    }
		    break;
		/**
		* Dumps a variable to firebug's server panel
		*/
		case 'FirePHP::DUMP':
		    if (is_array($message['body']))
		    {
			$key = Arr::path($message, 'body.key', '');
			$variable = Arr::path($message, 'body.variable', $message['body']);
			$this->_firephp->dump($key, $variable);
		    }
		    else
		    {
			$this->_firephp->log(strtr($format, $message));
		    }
		    break;
		/**
		* Displays a stack trace in firebug console
		*/
		case 'FirePHP::TRACE';
		    $this->_firephp->trace(strtr($format, $message));
		    break;
		/**
		* Displays an table in firebug console
		*/
		case 'FirePHP::TABLE':
		    if (is_array($message['body']))
		    {
			$label = Arr::path($message, 'body.label', '');
			$table = Arr::path($message, 'body.table', '');
			$this->_firephp->table($label, $table);
		    }
		    else
		    {
			$this->_firephp->log(strtr($format, $message));
		    }
		    break;
		/**
		* Starts a group in firebug console
		*/
		case 'FirePHP::GROUP_START';
		    $this->_firephp->group(strtr($format, $message));
		    break;
		/**
		* Ends a group in firebug console
		*/
		case 'FirePHP::GROUP_END';
		    $this->_firephp->groupEnd();
		    break;
		default:
		    $this->_firephp->log(strtr($format, $message));
	    }
	}
    }

} // End Kohana_Log_File