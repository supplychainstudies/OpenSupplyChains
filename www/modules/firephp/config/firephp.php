<?php defined('SYSPATH') OR die('No direct access allowed.');

return array (
	'default' => array(
		// Enables FirePHP output
		'enabled' => TRUE,

		// FirePHP Library Options
		'firephp' => array(
			'maxObjectDepth' => 10,
			'maxArrayDepth' => 20,
			'useNativeJsonEncode' => TRUE,
			'includeLineNumbers' => TRUE,
		),

		// Auth Module Integration, limits who has access to FirePHP
		'auth' => array(
			'enabled' => FALSE,        // Set to TRUE to enable
			'logged_in' => array(      // 'logged_in' => TRUE for all logged in users
				'admin'   => TRUE, // Roles... Use Admin or...
				'firephp' => TRUE, // Create a new firephp role
			),
		),

		// Table options
		'tables' => array(
			'show_objects' => TRUE,
		),

		// Database Queries
		'database' => array(
			'group' => TRUE,  // Save all queries and output them out as one group. Careful, could use a lot of memory
			'select' => TRUE, // Show all Select Queries
			'insert' => TRUE, // Show all Insert Queries
			'update' => TRUE, // Show all Update Queries
			'other' => TRUE,  // Show all Other  Queries
			'rows' => 10,     // Limit Rows displayed
		),

		// Kohana::$log options
		'log' => array(
			// Custom File Writer Options
			'file' => array(
				'format' => 'time --- type: body', // Format string
				'exclude' => array(		   // Don't write these types to file
					'FirePHP::LOG',
					'FirePHP::INFO',
					'FirePHP::WARN',
					'FirePHP::ERROR',
					'FirePHP::DUMP',
					'FirePHP::TRACE',
					'FirePHP::TABLE',
					'FirePHP::GROUP_START',
					'FirePHP::GROUP_END',
				)
			),
			// FireBug Console Writer Options
			'console' => array(
				'format' => 'time --- type: body', // Format String
				'exclude' => NULL,                 // Exclude the types from writing to the console
			)
		)
	)
);	