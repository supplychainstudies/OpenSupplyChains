<?php
return array(
    'email' => array(
        'not_empty' => 'A valid email address is required.',
        'email' => 'A valid email address is required.'
    ),
    'username' => array(
        'default' => 'Please enter a unique username.'
    ),
    'password' => array(
        'not_empty' => 'Please enter a password.'
    ),
    'password_confirm' => array(
        'default' => 'Your password needs to match.'
    ),
	'confirm_terms' => array(
	    'not_empty' => 'Please agree to the TOS.'
	)
);
