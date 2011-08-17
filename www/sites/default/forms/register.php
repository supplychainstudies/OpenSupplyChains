<?php

return array(
    'fields' => array(
        'email' => array(
            'type' => 'text',
            'label' => 'Email Address',
			'attributes' => array(
				'maxlength' => 100,
				'placeholder' => 'Enter your email address.'				
			)
        ),
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
            'attributes' => array(
                'maxlength' => 32,
				'placeholder' => 'Please choose a unique username.'
            )
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password'
        ),
        'password_confirm' => array(
            'type' => 'password',
            'label' => 'Password (Repeat)'
        ),
	    'terms' => array(
	        'type' => 'textarea',
	        'label' => 'Terms and Conditions'
	    ),
		'sourcemaporg_account' => array(
	        'type' => 'text',
	        'label' => 'If you have a Sourcemap.org account, enter it here.'
	    ),
        'confirm_terms' => array(
            'type' => 'checkbox',
            'label' => 'I have read and agree to the terms of service.',
            'default' => 1
        ),
        'register' => array(
            'type' => 'submit',
            'label' => 'Register'
        )
    ),
    'messages_file' => 'forms/register',
    'rules' => array(
        // todo: wildcard
        //'*' => array(),
        'email' => array(
            array('not_empty', array('email'))
        ),
	    'username' => array(
	        array('not_empty', array('alphadash'))
	    ),
        'password' => array(
	        array('not_empty')
        ),
        'password_confirm' => array(
            array('matches', array("password"))
        ),
	    'confirm_terms' => array(
	        array('not_empty')
	    ),
    ),
    'filters' => array()
);
