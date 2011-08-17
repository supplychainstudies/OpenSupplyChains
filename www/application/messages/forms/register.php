<?php
return array(
    'email' => array(
        'not_empty' => 'A valid email address is required.',
        'email' => 'A valid email address is required.'
    ),
    'username' => array(
        'default' => 'A username of letters, numbers, underscores, and dashes is required.'
    ),
    'password' => array(
        'default' => 'Your password must be between 4 and 32 characters.'
    ),
    'password_confirm' => array(
        'default' => 'Please confirm your password.'
    ),
	'confirm_terms' => array(
	    'default' => 'Please agree to the terms and conditions.'
	)
);
