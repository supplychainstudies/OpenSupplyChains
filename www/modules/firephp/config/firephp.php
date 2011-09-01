<?php defined('SYSPATH') OR die('No direct access allowed.');
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