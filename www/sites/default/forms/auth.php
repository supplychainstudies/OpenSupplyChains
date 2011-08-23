<?php

return array(
    'fields' => array(
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
			'attributes' => array(
				'maxlength' => 55,
			)
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
			'attributes' => array(
				'maxlength' => 55,
			)
        ),
        'auth' => array(
            'type' => 'submit',
            'label' => 'Login'
        )
    ),
    'messages_file' => 'forms/auth',
    'rules' => array(
        // todo: wildcard
        //'*' => array(),
        'username' => array(
            array('not_empty')
        ),
        'password' => array(
            array('not_empty')
        )
    ),
    'filters' => array()
);
